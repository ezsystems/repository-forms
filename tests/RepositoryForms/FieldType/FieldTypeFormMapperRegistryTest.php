<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\FieldType;

use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperRegistry;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

class FieldTypeFormMapperRegistryTest extends TestCase
{
    public function testGetAddMappers()
    {
        $registry = new FieldTypeFormMapperRegistry();
        self::assertSame([], $registry->getMappers());

        $mapper1 = $this->createMock(FieldTypeFormMapperInterface::class);
        $identifier1 = 'foo';
        $registry->addMapper($mapper1, $identifier1);
        $identifier2 = 'bar';
        $mapper2 = $this->createMock(FieldTypeFormMapperInterface::class);
        $registry->addMapper($mapper2, $identifier2);
        self::assertSame([
            $identifier1 => $mapper1,
            $identifier2 => $mapper2,
        ], $registry->getMappers());
    }

    public function testHasMapper()
    {
        $registry = new FieldTypeFormMapperRegistry();
        $identifier = 'foo';
        self::assertFalse($registry->hasMapper($identifier));
        $registry->addMapper($this->createMock(FieldTypeFormMapperInterface::class), $identifier);
        self::assertTrue($registry->hasMapper($identifier));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetMapperNoMapper()
    {
        $registry = new FieldTypeFormMapperRegistry();
        $registry->getMapper('foo');
    }

    public function testGetMapper()
    {
        $registry = new FieldTypeFormMapperRegistry();
        $mapper = $this->createMock(FieldTypeFormMapperInterface::class);
        $registry->addMapper($mapper, 'foo');
        self::assertSame($mapper, $registry->getMapper('foo'));
    }

    public function testMapFieldDefinitionFormNoMapper()
    {
        $registry = new FieldTypeFormMapperRegistry();
        $form = $this->createMock(FormInterface::class);
        $data = $this->createMock(FieldDefinitionData::class);
        $data
            ->expects($this->once())
            ->method('getFieldTypeIdentifier')
            ->willReturn(null);

        $registry->mapFieldDefinitionForm($form, $data);
    }

    public function testMapFieldDefinitionForm()
    {
        $mapper = $this->createMock(FieldTypeFormMapperInterface::class);
        $registry = new FieldTypeFormMapperRegistry();
        $registry->addMapper($mapper, 'ezstring');
        $form = $this->createMock(FormInterface::class);
        $data = $this->createMock(FieldDefinitionData::class);
        $data
            ->expects($this->once())
            ->method('getFieldTypeIdentifier')
            ->willReturn('ezstring');

        $mapper
            ->expects($this->once())
            ->method('mapFieldDefinitionForm')
            ->with($form, $data);
        $registry->mapFieldDefinitionForm($form, $data);
    }
}
