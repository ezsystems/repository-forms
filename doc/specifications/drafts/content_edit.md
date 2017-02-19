# Symfony forms content edit

## Synopsis
repository-forms currently supports creating content without an intermediate draft.

Support must be added for editing content. It implies creating a new version of an existing content,
rendering a form with the previous version's fields values, and allowing the user to save the draft,
cancel or publish the new version. It should also allow choosing a language, either for translation
or 

Ideally, the draft would only be created when we need it, not on the first display.
However, any interactive action on Fields (custom buttons, etc) will require that the draft
is created.  Note that the exact same problem exists in createWithoutDraft. We just don't
have any interactive FieldTypes at the moment.

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
