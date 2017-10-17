# Backwards compatibility changes

Changes affecting version compatibility with former or future versions.

## Changes

- EZP-27628: Implement editing support for RichText FieldType

  What changed: `ezrepoforms.field_type.form_mapper.ezrichtext` requires `ezpublish.api.service.field_type` and `ezpublish.fieldType.ezrichtext.converter.edit.xhtml5` dependencies.

  How it might affect your code: If you are implementing a subclass, make sure you are injecting required services.


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


## Removed features

- `\EzSystems\RepositoryForms\FieldType\DataTransformer\CountryValueTransformer`
  What changed: `\EzSystems\RepositoryForms\FieldType\DataTransformer\CountryValueTransformer` has been removed.

  How it might affect your code: If you were extending or using `CountryValueTransformer` you can now use `MultipleCountryValueTransformer` which is 1:1 functionality-wise.


- `EzSystems\RepositoryFormsBundle\Controller\ContentEditController`
  What changed: Removed `$pagelayout` property as well as `setPagelayout()` method.

  How it might affect your code: If you were using it to inject your page layout, you now have to utilize `@ezrepoforms.view_templates_listener` service. By default it's using pagelayout from resolved configuration (i.e. pagelayout set for siteaccess).
