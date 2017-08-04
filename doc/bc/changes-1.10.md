# Backwards compatibility changes

Changes affecting version compatibility with former or future versions.

## Changes
- EZP-27628: Implement editing support for RichText FieldType

  What changed: `ezrepoforms.field_type.form_mapper.ezrichtext` requires `ezpublish.api.service.field_type` and `ezpublish.fieldType.ezrichtext.converter.edit.xhtml5` dependencies.

  How it might affect your code: If you are implementing a subclass, make sure you are injecting required services.
  
- EZP-27641: Implement editing support for Relation FieldType

  What changed: Removed `ezpublish.translation_helper` dependency from `ezrepoforms.field_type.form_mapper.abstractrelation` service.

  How it might affect your code: Don't rely on `TranslationHelper` as the same functionality is now achieved by calling `getNames()` directly on the object.

## Deprecations
- EZP-27633: Implement editing support for Country FieldType

  What changed: `\EzSystems\RepositoryForms\FieldType\DataTransformer\CountryValueTransformer` is deprecated. It will be removed in 2.0.

  How it might affect your code: BC is preserved for the time being but it's advised to use `\EzSystems\RepositoryForms\FieldType\DataTransformer\MultipleCountryValueTransformer` which can be done without any effort besides class name change.

 - The "ezrepoforms.user_register.view_templates_listener" service is deprecated since 1.10 and will be removed in 2.0. Use "ezrepoforms.view_templates_listener" instead
   
## Removed features
