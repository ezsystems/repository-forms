<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\FieldType\Mapper;

use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\FieldType\Mapper\FormTypeBasedFieldValueFormMapper;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FormTypeBasedFieldValueFormMapperTest extends BaseMapperTest
{
    public function testMapFieldValueFormNoLanguageCode()
    {
        $mapper = new FormTypeBasedFieldValueFormMapper($this->fieldTypeService);

        $fieldDefinition = new FieldDefinition([
            'names' => [],
            'isRequired' => false,
            'fieldSettings' => ['isMultiple' => false, 'options' => []]
        ]);

        $this->data->expects($this->once())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $mapper->mapFieldValueForm($this->fieldForm, $this->data);
    }

    public function testMapFieldValueFormWithLanguageCode()
    {
        $mapper = new FormTypeBasedFieldValueFormMapper($this->fieldTypeService);

        $fieldDefinition = new FieldDefinition([
            'names' => ['eng-GB' => 'foo'],
            'isRequired' => false,
            'fieldSettings' => ['isMultiple' => false, 'options' => []]
        ]);
        $this->data->expects($this->once())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $mapper->mapFieldValueForm($this->fieldForm, $this->data);
    }
}
