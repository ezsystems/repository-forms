<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\FieldType\Mapper;

use EzSystems\RepositoryForms\FieldType\Mapper\FormTypeBasedFieldValueFormMapper;

class FormTypeBasedFieldValueFormMapperTest extends \PHPUnit_Framework_TestCase
{
    private $fieldTypeService;

    protected function setUp()
    {
        $this->fieldTypeService = $this->getMockBuilder(\eZ\Publish\API\Repository\FieldTypeService::class)
            ->getMock();
        $this->fieldTypeService
            ->expects($this->any())
            ->method('getFieldType')
            ->willReturn($this->getMockBuilder(\eZ\Publish\API\Repository\FieldType::class)->getMock());
    }

    public function testMapFieldValueFormNoLanguageCode()
    {
        $mapper = new FormTypeBasedFieldValueFormMapper($this->fieldTypeService);

        $config = $this->getMockBuilder(\Symfony\Component\Form\FormConfigInterface::class)->getMock();
        $config->expects($this->once())
            ->method('getOption')
            ->with('languageCode')
            ->willReturn(false);

        $formFactory = $this->getMockBuilder(\Symfony\Component\Form\FormFactoryInterface::class)
            ->setMethods(['addModelTransformer', 'setAutoInitialize', 'getForm'])
            ->getMockForAbstractClass();
        $formFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($formFactory);
        $formFactory->expects($this->once())
            ->method('create')
            ->willReturn($formFactory);
        $formFactory->expects($this->once())
            ->method('addModelTransformer')
            ->willReturn($formFactory);
        $formFactory->expects($this->once())
            ->method('setAutoInitialize')
            ->willReturn($formFactory);

        $config->expects($this->once())
            ->method('getFormFactory')
            ->willReturn($formFactory);

        $fieldForm = $this->getMockBuilder(\Symfony\Component\Form\FormInterface::class)->getMock();
        $fieldForm->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $data = $this->getMockBuilder(\EzSystems\RepositoryForms\Data\Content\FieldData::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fieldDefinition = $this->getMockBuilder(\eZ\Publish\API\Repository\Values\ContentType\FieldDefinition::class)
            ->getMock();
        $fieldDefinition->expects($this->once())
            ->method('getName')
            ->willReturn(false);
        $fieldDefinition->expects($this->once())
            ->method('getNames')
            ->willReturn(['foo']);
        $fieldDefinition
            ->expects($this->any())
            ->method('__get')
            ->will(
                $this->returnValueMap([
                    ['isRequired', false],
                    ['fieldSettings', ['isMultiple' => false, 'options' => []]]
                ])
            );

        $data->expects($this->atLeastOnce())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $mapper->mapFieldValueForm($fieldForm, $data);
    }

    public function testMapFieldValueFormWithLanguageCode()
    {
        $mapper = new FormTypeBasedFieldValueFormMapper($this->fieldTypeService);

        $config = $this->getMockBuilder(\Symfony\Component\Form\FormConfigInterface::class)->getMock();
        $config->expects($this->once())
            ->method('getOption')
            ->with('languageCode')
            ->willReturn('eng-GB');

        $formFactory = $this->getMockBuilder(\Symfony\Component\Form\FormFactoryInterface::class)
            ->setMethods(['addModelTransformer', 'setAutoInitialize', 'getForm'])
            ->getMockForAbstractClass();
        $formFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($formFactory);
        $formFactory->expects($this->once())
            ->method('create')
            ->willReturn($formFactory);
        $formFactory->expects($this->once())
            ->method('addModelTransformer')
            ->willReturn($formFactory);
        $formFactory->expects($this->once())
            ->method('setAutoInitialize')
            ->willReturn($formFactory);

        $config->expects($this->once())
            ->method('getFormFactory')
            ->willReturn($formFactory);

        $fieldForm = $this->getMockBuilder(\Symfony\Component\Form\FormInterface::class)->getMock();
        $fieldForm->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $data = $this->getMockBuilder(\EzSystems\RepositoryForms\Data\Content\FieldData::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fieldDefinition = $this->getMockBuilder(\eZ\Publish\API\Repository\Values\ContentType\FieldDefinition::class)
            ->getMock();
        $fieldDefinition->expects($this->once())
            ->method('getName')
            ->willReturn('bar');
        $fieldDefinition->expects($this->once())
            ->method('getNames')
            ->willReturn(['foo']);
        $fieldDefinition
            ->expects($this->any())
            ->method('__get')
            ->will(
                $this->returnValueMap([
                    ['isRequired', false],
                    ['fieldSettings', ['isMultiple' => false, 'options' => []]]
                ])
            );
        $data->expects($this->atLeastOnce())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $mapper->mapFieldValueForm($fieldForm, $data);
    }
}
