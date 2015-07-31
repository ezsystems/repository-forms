<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use LogicException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to register FieldType form mappers.
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

                $registry->addMethodCall('addMapper', [new Reference($id), $attribute['fieldType']]);
            }
        }
    }
}
