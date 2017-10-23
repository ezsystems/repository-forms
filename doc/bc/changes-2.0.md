# Backwards compatibility changes

Changes affecting version compatibility with former or future versions.

## Changes
- EZP-27641: Implement editing support for Relation FieldType

  What changed: Removed `ezpublish.translation_helper` dependency from `ezrepoforms.field_type.form_mapper.abstractrelation` service.

  How it might affect your code: Don't rely on `TranslationHelper` as the same functionality is now achieved by calling `getNames()` directly on the object.
  
- EZP-28038: As a Developer I would like to have Symfony form Types representing every eZ Platform fieldtype

  What changed:
        - `CountryFormMapper`: removed dependency on `%ezpublish.fieldType.ezcountry.data%`
        - `FloatFormMapper`: removed dependency on `@ezpublish.api.service.field_type`
        - `IntegerFormMapper`: removed dependency on `@ezpublish.api.service.field_type`
        - `ISBNFormMapper`: removed dependency on `@ezpublish.api.service.field_type`
        - `MapLocationFormMapper`: removed dependency on `@ezpublish.api.service.field_type`
        - `RichTextFormMapper`: removed dependency on `@ezpublish.api.service.field_type` and `@ezpublish.fieldType.ezrichtext.converter.edit.xhtml5`
        - `TextBlockFormMapper`: removed dependency on `@ezpublish.api.service.field_type`
        - `TextLineFormMapper`: removed dependency on `@ezpublish.api.service.field_type`
        - `TimeFormMapper`: removed dependency on `@ezpublish.api.service.field_type`
        - `UrlFormMapper`: removed dependency on `@ezpublish.api.service.field_type`
    
  How it might affect your code: If you are extending those classes you are no longer required to provide above dependencies.
- `EzSystems\RepositoryForms\Form\Type\FieldValue\Author\{AuthorCollectionType,AuthorEntryType}` were moved to `EzSystems\RepositoryForms\Form\Type\FieldType\Author\` namespace.

  How it might affect your code: You are required to change all occurrences of these classes in import sections.
  
## Deprecations
- EZP-27633: Implement editing support for Country FieldType

  What changed: `\EzSystems\RepositoryForms\FieldType\DataTransformer\CountryValueTransformer` is deprecated. It will be removed in 2.0.

  How it might affect your code: BC is preserved for the time being but it's advised to use `\EzSystems\RepositoryForms\FieldType\DataTransformer\MultipleCountryValueTransformer` which can be done without any effort besides class name change.

 - The "ezrepoforms.user_register.view_templates_listener" service is deprecated since 1.10 and will be removed in 2.0. Use "ezrepoforms.view_templates_listener" instead
   
## Removed features
