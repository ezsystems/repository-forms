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
    const FIELD_TYPE_FORM_MAPPER_DISPATCHER = 'ezrepoforms.field_type_form_mapper.dispatcher';

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
                        'ez.fieldFormMapper or ezplatform.field_type.form_mapper service tags need a "fieldType" attribute to identify which field type the mapper is for. None given.'
                    );
                }

                $dispatcherDefinition->addMethodCall('addMapper', [new Reference($id), $tag['fieldType']]);
            }
        }
    }

    /**
     * Gathers services tagged as either
     * - ez.fieldFormMapper.value
     * - ez.fieldFormMapper.definition
     * - ezplatform.field_type.form_mapper.value
     * - ezplatform.field_type.form_mapper.definition.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return array
     */
    private function findTaggedFormMapperServices(ContainerBuilder $container): array
    {
        $ezFieldFormMapperValueTags = $container->findTaggedServiceIds('ez.fieldFormMapper.value');
        $ezFieldFormMapperDefinitionTags = $container->findTaggedServiceIds('ez.fieldFormMapper.definition');
        $ezplatformFieldFormMapperValueTags = $container->findTaggedServiceIds('ezplatform.field_type.form_mapper.value');
        $ezplatformFieldFormMapperDefinitionTags = $container->findTaggedServiceIds('ezplatform.field_type.form_mapper.definition');

        foreach ($ezFieldFormMapperValueTags as $ezFieldFormMapperValueTag) {
            @trigger_error('`ez.fieldFormMapper.value` service tag is deprecated and will be removed in version 9. Please use `ezplatform.field_type.form_mapper.value` instead.', E_USER_DEPRECATED);
        }

        foreach ($ezFieldFormMapperDefinitionTags as $ezFieldFormMapperValueTag) {
            @trigger_error('`ez.fieldFormMapper.definition` service tag is deprecated and will be removed in version 9. Please use `ezplatform.field_type.form_mapper.definition` instead.', E_USER_DEPRECATED);
        }

        return array_merge(
            $ezFieldFormMapperValueTags,
            $ezFieldFormMapperDefinitionTags,
            $ezplatformFieldFormMapperValueTags,
            $ezplatformFieldFormMapperDefinitionTags
        );
    }
}
