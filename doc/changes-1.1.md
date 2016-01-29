# Changes in version 1.1

## Deprecations
- `FieldTypeFormMapperInterface` is deprecated, and replaced by `FieldDefinitionFormMapperInterface`
- The FieldType form mapper registry, associated services, classes and interfaces are deprecated, and will be removed
  in 2.0:
    - `ezrepoforms.field_type_form_mapper.registry` service
    - `EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperRegistry` class
    - `EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperRegistryInterface` interface
    - `EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\FieldTypeFormMapperPass` compiler pass
  The new `ezrepoforms.field_type_form_mapper.registry` should be used instead.
