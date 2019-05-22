<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use LogicException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to register FieldType form mappers in the mapper dispatcher.
 */
class FieldTypeFormMapperDispatcherPass implements CompilerPassInterface
{
    public const FIELD_TYPE_FORM_MAPPER_DISPATCHER = 'ezrepoforms.field_type_form_mapper.dispatcher';
    public const FIELD_FORM_MAPPER_VALUE = 'ez.fieldFormMapper.value';
    public const FIELD_FORM_MAPPER_DEFINITION = 'ez.fieldFormMapper.definition';
    public const EZPLATFORM_FIELD_TYPE_FORM_MAPPER_VALUE = 'ezplatform.field_type.form_mapper.value';
    public const EZPLATFORM_FIELD_TYPE_FORM_MAPPER_DEFINITION = 'ezplatform.field_type.form_mapper.definition';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::FIELD_TYPE_FORM_MAPPER_DISPATCHER)) {
            return;
        }

        $dispatcherDefinition = $container->findDefinition(self::FIELD_TYPE_FORM_MAPPER_DISPATCHER);

        foreach ($this->findTaggedFormMapperServices($container) as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['fieldType'])) {
                    throw new LogicException(
                        '`ezplatform.field_type.form_mapper` or deprecated `ez.fieldFormMapper` service tags need a "fieldType" attribute to identify which field type the mapper is for. None given.'
                    );
                }

                $dispatcherDefinition->addMethodCall('addMapper', [new Reference($id), $tag['fieldType']]);
            }
        }
    }

    /**
     * Gathers services tagged as either
     * - ez.fieldFormMapper.value (deprecated)
     * - ez.fieldFormMapper.definition (deprecated)
     * - ezplatform.field_type.form_mapper.value
     * - ezplatform.field_type.form_mapper.definition.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return array
     */
    private function findTaggedFormMapperServices(ContainerBuilder $container): array
    {
        $ezFieldFormMapperValueTags = $container->findTaggedServiceIds(self::FIELD_FORM_MAPPER_VALUE);
        $ezFieldFormMapperDefinitionTags = $container->findTaggedServiceIds(self::FIELD_FORM_MAPPER_DEFINITION);
        $ezplatformFieldFormMapperValueTags = $container->findTaggedServiceIds(self::EZPLATFORM_FIELD_TYPE_FORM_MAPPER_VALUE);
        $ezplatformFieldFormMapperDefinitionTags = $container->findTaggedServiceIds(self::EZPLATFORM_FIELD_TYPE_FORM_MAPPER_DEFINITION);

        foreach ($ezFieldFormMapperValueTags as $ezFieldFormMapperValueTag) {
            @trigger_error(
                sprintf(
                    '`%s` service tag is deprecated and will be removed in eZ Platform 4.0. Please use `%s` instead.',
                    self::FIELD_FORM_MAPPER_VALUE,
                    self::EZPLATFORM_FIELD_TYPE_FORM_MAPPER_VALUE
                ),
                E_USER_DEPRECATED
            );
        }

        foreach ($ezFieldFormMapperDefinitionTags as $ezFieldFormMapperValueTag) {
            @trigger_error(
                sprintf(
                    '`%s` service tag is deprecated and will be removed in eZ Platform 4.0. Please use `%s` instead.',
                    self::FIELD_FORM_MAPPER_DEFINITION,
                    self::EZPLATFORM_FIELD_TYPE_FORM_MAPPER_DEFINITION
                ),
                E_USER_DEPRECATED
            );
        }

        return array_merge(
            $ezFieldFormMapperValueTags,
            $ezFieldFormMapperDefinitionTags,
            $ezplatformFieldFormMapperValueTags,
            $ezplatformFieldFormMapperDefinitionTags
        );
    }
}
