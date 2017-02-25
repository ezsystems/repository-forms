# Symfony forms content edit

## Synopsis
repository-forms currently supports creating content without an intermediate draft.

Support must be added for editing content. It implies creating a new version of an existing content,
rendering a form with the previous version's fields values, and allowing the user to save the draft,
cancel or publish the new version. It should also allow choosing a language, either for translation
or 

### Draft management
Ideally, the draft would only be created when we need it, not on the first display.

However, any interactive action on Fields (custom buttons, etc) will require that the draft
is created.  Note that the exact same problem exists in createWithoutDraft. We just don't
have any interactive FieldTypes at the moment.

## Prototypes

### Creating a draft from an existing version

> A route that displays a form allowing to create a draft from an existing content item.

- path: `/content/create/draft/{contentId}/{fromVersionNo}/{fromLanguage}/{toLanguage}`
- controller action: `ContentEditController::createContentDraftAction`

All the arguments except for `contentId` are optional. Arguments that are specified will
be used as default values for the form. The draft will only be created when the form is 
submitted. However, this resource is meant to be used by other parts of the system to create 
a content draft.

When the form is submitted and a draft is created, a redirection to the content editing route
will be issued.

#### Architecture
`ContentFormProcessor::processCreateDraft()` uses a `ContentCreateDraftType` Form.
It manipulates a `ContentCreateDraftData` object that has the parameters to create the draft.
The actual creation of the draft is delegated to the FormProcessor API, by firing a
`RepositoryFormEvents::CONTENT_CREATE_DRAFT`.

#### Tasks

##### Versions conflits
Decide how [version conflicts](https://jira.ez.no/browse/EZP-25465) should be handled.
 
There are discussions about moving this logic down to the API. On the other hand, it would
be fairly simple to implement that logic in this controller.

##### Clarify language handling
Verify that language handling makes sense.

### Editing a draft
> A route that displays a content editing form, with options to discard, save or publish.

- path: `/content/edit/{contentId}/{versionNo}/{language}`
- controller action: `ContentEditController::editContentDraftAction`

`language` is optional.

Will load the requested content, in the requested version, and show the edit form for the fields.
Uses the same exact form than the content edit one.

#### Architecture
The Form, `ContentEditType`, uses a `ContentUpdateData` object.

## Additional topics

### Form rendering customization
In order to benefit from the save level of flexibility legacy provided, the first thing that comes
to mind is views: if we have a ContentEditView, we can easily override the template and customize
the form using [form theming](https://symfony.com/doc/current/form/form_customization.html#form-theming-in-twig).

See https://github.com/ezsystems/ezplatform-demo/blob/master/app/Resources/views/user/registration_content_form.html.twig
for an example of a content form customization.

This involves:
- ViewBuilders
- ViewProviders + configuration
- Default templates
- Practical examples (anything in `ez*-demo` ?)

> Very down-to-earth idea: if `fieldsData` was indexed by field def identifier, it would simplify
> the template code a lot.

## Background research

### How repository forms does it for content types 
To provide edition of ContentTypes, `\EzSystems\PlatformUIBundle\Controller\ContentTypeController::updateContentTypeAction`
will attempt to load a draft for the cotnent, and create one if there isn't. But this only works because
ContentTypes have a unique draft mechanism. Content drafts have a different approach, where versions
_belong_ to a user and a user may have several drafts of the same content at a given time.

### How legacy does it for content
In legacy, an action is responsible for creating a new draft. It will by default do so
for the currently published version, but has an option to use a different version as a source.
If not specified, the target language and translation mode (edit or translate) will be asked.
After creating the draft, it will redirect the user to another action where the draft is edited, then
either cancelled, saved or published.

### How PlatformUI does it
Version & conflict management has recently ([6 month ago](https://jira.ez.no/browse/EZP-25465)) been added
to PlatformUI. We may want to use it as a basis for the symfony implementation. 
