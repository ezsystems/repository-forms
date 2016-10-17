<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\FieldType\Mapper;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\FieldType\Mapper\CheckboxFormMapper;

class CheckboxFormMapperTest extends BaseMapperTest
{
    public function testMapFieldValueFormNoLanguageCode()
    {
        $mapper = new CheckboxFormMapper($this->fieldTypeService);

        $fieldDefinition = new FieldDefinition(['names' => []]);

        $this->data->expects($this->once())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $mapper->mapFieldValueForm($this->fieldForm, $this->data);
    }

    public function testMapFieldValueFormWithLanguageCode()
    {
        $mapper = new CheckboxFormMapper($this->fieldTypeService);

        $fieldDefinition = new FieldDefinition(['names' => ['eng-GB' => 'foo']]);

        $this->data->expects($this->once())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $mapper->mapFieldValueForm($this->fieldForm, $this->data);
    }
}
