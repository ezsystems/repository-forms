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
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\FieldType\Mapper\TextBlockFormMapper;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class TextBlockFormMapperTest extends \PHPUnit_Framework_TestCase
{
    private $fieldTypeService;

    protected function setUp()
    {
        $this->fieldTypeService = $this->getMockBuilder(FieldTypeService::class)
            ->getMock();
        $this->fieldTypeService
            ->expects($this->any())
            ->method('getFieldType')
            ->willReturn($this->getMockBuilder(FieldType::class)->getMock());
    }

    public function testMapFieldValueFormNoLanguageCode()
    {
        $mapper = new TextBlockFormMapper($this->fieldTypeService);

        $config = $this->getMockBuilder(FormConfigInterface::class)->getMock();
        $config->expects($this->once())
            ->method('getOption')
            ->with('languageCode')
            ->willReturn(false);

        $formFactory = $this->getMockBuilder(FormFactoryInterface::class)
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

        $fieldForm = $this->getMockBuilder(FormInterface::class)->getMock();
        $fieldForm->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $data = $this->getMockBuilder(FieldData::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fieldDefinition = $this->getMockBuilder(FieldDefinition::class)
            ->getMock();
        $fieldDefinition->expects($this->once())
            ->method('getName')
            ->willReturn(false);
        $fieldDefinition->expects($this->once())
            ->method('getNames')
            ->willReturn(['foo']);

        $data->expects($this->atLeastOnce())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $mapper->mapFieldValueForm($fieldForm, $data);
    }

    public function testMapFieldValueFormWithLanguageCode()
    {
        $mapper = new TextBlockFormMapper($this->fieldTypeService);

        $config = $this->getMockBuilder(FormConfigInterface::class)->getMock();
        $config->expects($this->once())
            ->method('getOption')
            ->with('languageCode')
            ->willReturn('eng-GB');

        $formFactory = $this->getMockBuilder(FormFactoryInterface::class)
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

        $fieldForm = $this->getMockBuilder(FormInterface::class)->getMock();
        $fieldForm->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $data = $this->getMockBuilder(FieldData::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fieldDefinition = $this->getMockBuilder(FieldDefinition::class)
            ->getMock();
        $fieldDefinition->expects($this->once())
            ->method('getName')
            ->willReturn('bar');
        $fieldDefinition->expects($this->once())
            ->method('getNames')
            ->willReturn(['foo']);

        $data->expects($this->atLeastOnce())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $mapper->mapFieldValueForm($fieldForm, $data);
    }
}
