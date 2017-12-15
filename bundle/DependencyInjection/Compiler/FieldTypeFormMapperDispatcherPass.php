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
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezrepoforms.field_type_form_mapper.dispatcher')) {
            return;
        }

        $dispatcherDefinition = $container->findDefinition('ezrepoforms.field_type_form_mapper.dispatcher');

        foreach ($this->findTaggedFormMapperServices($container) as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['fieldType'])) {
                    throw new LogicException(
                        'ez.fieldFormMapper service tags need a "fieldType" attribute to identify which field type the mapper is for. None given.'
                    );
                }

                $dispatcherDefinition->addMethodCall('addMapper', [new Reference($id), $tag['fieldType']]);
            }
        }
    }

    /**
     * Gathers services tagged as either ez.fieldFormMapper.value or ez.fieldFormMapper.definition, as well
     * as the deprecated ez.fieldType.formMapper.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return array
     */
    private function findTaggedFormMapperServices(ContainerBuilder $container)
    {
        $formMappers = $container->findTaggedServiceIds('ez.fieldType.formMapper');
        foreach (array_keys($formMappers) as $serviceId) {
            @trigger_error(
                "The ez.fieldType.formMapper service tag from $serviceId is deprecated in ezsystems/repository-forms 1.3, " .
                "and will be removed in version 2.0\n" .
                'Use ez.fieldFormMapper.value and/or ez.fieldFormMapper.definition instead.',
                E_USER_DEPRECATED
            );
        }

        return
            $container->findTaggedServiceIds('ez.fieldFormMapper.value') +
            $container->findTaggedServiceIds('ez.fieldFormMapper.definition') +
            $formMappers;
    }
}
