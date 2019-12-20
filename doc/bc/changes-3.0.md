# Backwards compatibility changes

Forms located in `repository-forms` have been moved to other packages.

Content Type editing, including Action Dispatchers, Form Processors, Types and Data classes related to Content Types/Limitations, has been moved to `ezsystems/ezplatform-admin-ui`.

Forms for content creation have been moved to a new `ezsystems/ezplatform-content-forms` package.

The following locations have been changed:

|Former location|New location|
|---|---|
|EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface|EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface|
|EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface|EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface|
|EzSystems\RepositoryForms\Limitation\LimitationFormMapperInterface|EzSystems\EzPlatformAdminUi\Limitation\LimitationFormMapperInterface|
|EzSystems\RepositoryForms\Limitation\LimitationValueMapperInterface|EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface|

`repository-forms` remains as an additional layer ensuring that your custom implementations will still work as long as they are:
* Limitation Value/Form mappers
* Field Type (Definition) form mappers
* Form Processors/Event Listeners/Event Subscribers relying on `\EzSystems\RepositoryForms\Event\RepositoryFormEvents` class constants

More complicated use cases are not guaranteed to work. Please upgrade namespaces pointing to new locations.

Backwards compatibility support is planned to be dropped in eZ Platform version 4.0.
