# Symfony forms content edit

## Synopsis
repository-forms supports creating content with and without an intermediate draft. This describes
editing with drafts.

Editing content implies creating a new version of an existing content, rendering a form with the
previous version's fields values, and allowing the user to save the draft, cancel or publish the
new version.


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


### Editing a draft
> A route that displays a content editing form, with options to discard, save or publish.

- path: `/content/edit/draft/{contentId}/{versionNo}/{language}`
- controller action: `ContentEditController::editContentDraftAction`

`language` is optional.

Will load the requested content, in the requested version, and show the edit form for the fields.
Uses the same exact form as the content edit one.

#### Architecture
The Form, `ContentEditType`, uses a `ContentUpdateData` object.


## Future development

### Version conflits
[Version conflicts](https://jira.ez.no/browse/EZP-25465) must be handled. There are discussions
about moving this logic down to the API. On the other hand, it would be fairly simple to implement
that logic in this controller.

### Language handling
Translation is currently not implemented. New drafts are created in the same language as their
predecessors.
