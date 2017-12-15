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
 * Compiler pass to register FieldType form mappers.
 *
 * @deprecated Deprecated since version 1.1, will be removed in version 2.0. FieldTypeFormMapperDispatcher covers the
 *             same service tag, but using the dispatcher instead of the registry.
 */
class FieldTypeFormMapperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezrepoforms.field_type_form_mapper.registry')) {
            return;
        }

        $registry = $container->findDefinition('ezrepoforms.field_type_form_mapper.registry');

        foreach ($container->findTaggedServiceIds('ez.fieldType.formMapper') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['fieldType'])) {
                    throw new LogicException(
                        'ez.fieldType.formMapper service tag needs a "fieldType" attribute to identify which field type the mapper is for. None given.'
                    );
                }

                if ($this->isFieldTypeMapper($container, $id)) {
                    $registry->addMethodCall('addMapper', [new Reference($id), $attribute['fieldType']]);
                }
            }
        }
    }

    /**
     * Checks if service $id is a FieldTypeMapperInterface. The newly added FieldValueFormMapperInterface can not be
     * given to the registry.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param $id
     *
     * @return bool
     */
    private function isFieldTypeMapper(ContainerBuilder $container, $id)
    {
        $class = $container->findDefinition($id)->getClass();
        if (preg_match('/^%(.*)%$/', $class, $m)) {
            if ($container->hasParameter($m[1])) {
                $class = $container->getParameter($m[1]);
            }
        }

        return in_array(
            'EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface',
            class_implements($class)
        );
    }
}
