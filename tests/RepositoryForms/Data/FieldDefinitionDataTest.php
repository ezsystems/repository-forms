<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\tests\RepositoryForms\Data;

use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use PHPUnit_Framework_TestCase;

class FieldDefinitionDataTest extends PHPUnit_Framework_TestCase
{
    public function testFieldDefinition()
    {
        $fieldDefinition = $this->getMockForAbstractClass('\eZ\Publish\API\Repository\Values\ContentType\FieldDefinition');
        $data = new FieldDefinitionData(['fieldDefinition' => $fieldDefinition]);
        self::assertSame($fieldDefinition, $data->fieldDefinition);
    }

    public function testGetFieldTypeIdentifier()
    {
        $fieldTypeIdentifier = 'ezstring';
        $fieldDefinition = $this->getMockBuilder('\eZ\Publish\API\Repository\Values\ContentType\FieldDefinition')
            ->setConstructorArgs([['fieldTypeIdentifier' => $fieldTypeIdentifier]])
            ->getMockForAbstractClass();
        $data = new FieldDefinitionData(['fieldDefinition' => $fieldDefinition]);
        self::assertSame($fieldTypeIdentifier, $data->getFieldTypeIdentifier());
    }
}
