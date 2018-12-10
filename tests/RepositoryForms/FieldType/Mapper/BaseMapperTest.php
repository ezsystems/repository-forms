<?php

namespace EzSystems\RepositoryForms\Tests\FieldType\Mapper;

use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

abstract class BaseMapperTest extends TestCase
{
    protected $fieldTypeService;
    protected $config;
    protected $fieldForm;
    protected $data;

    protected function setUp()
    {
        $this->fieldTypeService = $this->getMockBuilder(FieldTypeService::class)
            ->getMock();
        $this->fieldTypeService
            ->expects($this->any())
            ->method('getFieldType')
            ->willReturn($this->getMockBuilder(FieldType::class)->getMock());

        $this->config = $this->getMockBuilder(FormConfigInterface::class)->getMock();

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

        $this->config->expects($this->once())
            ->method('getFormFactory')
            ->willReturn($formFactory);

        $this->fieldForm = $this->getMockBuilder(FormInterface::class)->getMock();
        $this->fieldForm->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->config);

        $this->data = $this->getMockBuilder(FieldData::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
