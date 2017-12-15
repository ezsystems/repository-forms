<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\tests\RepositoryForms\Data;

use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use PHPUnit\Framework\TestCase;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;

class FieldDefinitionDataTest extends TestCase
{
    public function testFieldDefinition()
    {
        $fieldDefinition = $this->getMockForAbstractClass(FieldDefinition::class);
        $data = new FieldDefinitionData(['fieldDefinition' => $fieldDefinition]);
        self::assertSame($fieldDefinition, $data->fieldDefinition);
    }

    public function testGetFieldTypeIdentifier()
    {
        $fieldTypeIdentifier = 'ezstring';
        $fieldDefinition = $this->getMockBuilder(FieldDefinition::class)
            ->setConstructorArgs([['fieldTypeIdentifier' => $fieldTypeIdentifier]])
            ->getMockForAbstractClass();
        $data = new FieldDefinitionData(['fieldDefinition' => $fieldDefinition]);
        self::assertSame($fieldTypeIdentifier, $data->getFieldTypeIdentifier());
    }
}
