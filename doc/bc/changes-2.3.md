# Backwards compatibility changes

Changes affecting version compatibility with former or future versions.

## Removed features

- EZP-29269: Editing content with user content field no longer results with draft creation.

  As part of it :
   - `\EzSystems\RepositoryForms\EventListener\UserEditListener` was removed 
   - New routes were introduced `ez_user_create` and `ez_user_update`
