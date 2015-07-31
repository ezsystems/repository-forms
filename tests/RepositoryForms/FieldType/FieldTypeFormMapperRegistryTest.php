<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Tests\FieldType;

use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperRegistry;
use PHPUnit_Framework_TestCase;

class FieldTypeFormMapperRegistryTest extends PHPUnit_Framework_TestCase
{
    public function testGetAddMappers()
    {
        $registry = new FieldTypeFormMapperRegistry();
        self::assertSame([], $registry->getMappers());

        $mapper1 = $this->getMock('\EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface');
        $identifier1 = 'foo';
        $registry->addMapper($mapper1, $identifier1);
        $identifier2 = 'bar';
        $mapper2 = $this->getMock('\EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface');
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
        $registry->addMapper($this->getMock('\EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface'), $identifier);
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
        $mapper = $this->getMock('\EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface');
        $registry->addMapper($mapper, 'foo');
        self::assertSame($mapper, $registry->getMapper('foo'));
    }

    public function testMapFieldDefinitionFormNoMapper()
    {
        $registry = new FieldTypeFormMapperRegistry();
        $form = $this->getMock('\Symfony\Component\Form\FormInterface');
        $data = $this->getMock('\EzSystems\RepositoryForms\Data\FieldDefinitionData');
        $data
            ->expects($this->once())
            ->method('getFieldTypeIdentifier')
            ->willReturn(null);

        $registry->mapFieldDefinitionForm($form, $data);
    }

    public function testMapFieldDefinitionForm()
    {
        $mapper = $this->getMock('\EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface');
        $registry = new FieldTypeFormMapperRegistry();
        $registry->addMapper($mapper, 'ezstring');
        $form = $this->getMock('\Symfony\Component\Form\FormInterface');
        $data = $this->getMock('\EzSystems\RepositoryForms\Data\FieldDefinitionData');
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
