# Backwards compatibility changes

Changes affecting version compatibility with former or future versions.

## Changes
- EZP-27641: Implement editing support for Relation FieldType

  What changed: Removed `ezpublish.translation_helper` dependency from `ezrepoforms.field_type.form_mapper.abstractrelation` service.

  How it might affect your code: Don't rely on `TranslationHelper` as the same functionality is now achieved by calling `getNames()` directly on the object.

## Deprecations

## Removed features
