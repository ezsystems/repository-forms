<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\Tests\DependencyInjection\Compiler;

use EzSystems\RepositoryForms\Limitation\LimitationValueMapperInterface;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\FieldTypeFormMapperDispatcherPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FieldTypeFormMapperDispatcherPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setDefinition(FieldTypeFormMapperDispatcherPass::FIELD_TYPE_FORM_MAPPER_DISPATCHER, new Definition());
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FieldTypeFormMapperDispatcherPass());
    }

    /**
     * @dataProvider tagsProvider
     */
    public function testRegisterMappers(string $tag)
    {
        $fieldTypeIdentifier = 'field_type_identifier';
        $serviceId = 'service_id';
        $def = new Definition();
        $def->addTag($tag, ['fieldType' => $fieldTypeIdentifier]);
//        $def->setClass(\get_class($this->createMock(LimitationValueMapperInterface::class)));
        $this->setDefinition($serviceId, $def);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            FieldTypeFormMapperDispatcherPass::FIELD_TYPE_FORM_MAPPER_DISPATCHER,
            'addMapper',
            [new Reference($serviceId), $fieldTypeIdentifier]
        );
    }

    public function tagsProvider(): array
    {
        return [
            ['ez.fieldFormMapper.value'],
            ['ez.fieldFormMapper.definition'],
            ['ezplatform.field_type.form_mapper.value'],
            ['ezplatform.field_type.form_mapper.definition'],
        ];
    }
}
