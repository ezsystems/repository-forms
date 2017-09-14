<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\Tests\DependencyInjection\Compiler;

use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\FieldTypeFormMapperPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface;

class FieldTypeFormMapperPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setDefinition('ezrepoforms.field_type_form_mapper.registry', new Definition());
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FieldTypeFormMapperPass());
    }

    public function testRegisterMappers()
    {
        $fieldTypeMapperServiceId = 'fieldtype_mapper';
        $def = new Definition();
        $def->addTag('ez.fieldType.formMapper', ['fieldType' => 'fieldtype1']);
        $def->setClass(get_class($this->createMock(FieldTypeFormMapperInterface::class)));
        $this->setDefinition($fieldTypeMapperServiceId, $def);

        $fieldValueMapperServiceId = 'fieldvalue_mapper';
        $def = new Definition();
        $def->addTag('ez.fieldType.formMapper', ['fieldType' => 'fieldtype2']);
        $def->setClass(get_class($this->createMock(FieldTypeFormMapperInterface::class)));
        $this->setDefinition($fieldValueMapperServiceId, $def);

        $this->compile();

        // But there should not be a mapper call with $fieldValueMapperServiceId
        $definition = $this->container->getDefinition('ezrepoforms.field_type_form_mapper.registry');
        foreach ($definition->getMethodCalls() as $methodCall) {
            list($method, $arguments) = $methodCall;
            $this->assertFalse($method === 'addMapper' && ($arguments[0] === new Reference($fieldTypeMapperServiceId)));
        }

        // There should be a mapper call with $fieldTypeMapperServiceId
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ezrepoforms.field_type_form_mapper.registry',
            'addMapper',
            [new Reference($fieldTypeMapperServiceId), 'fieldtype1']
        );
    }
}
