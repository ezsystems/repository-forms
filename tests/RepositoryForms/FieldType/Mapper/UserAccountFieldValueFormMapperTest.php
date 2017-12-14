<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\FieldType\Mapper;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\FieldType\Mapper\UserAccountFieldValueFormMapper;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;

class UserAccountFieldValueFormMapperTest extends BaseMapperTest
{
    protected function setUp()
    {
        parent::setUp();

        $formRoot = $this->getMockBuilder(FormInterface::class)->getMock();
        $userEditForm = $this->getMockBuilder(FormInterface::class)->getMock();
        $config = $this->getMockBuilder(FormConfigInterface::class)->getMock();

        $config->method('getOption')
            ->with('intent')
            ->willReturn('update');
        $formRoot->method('getConfig')
            ->willReturn($config);
        $userEditForm->method('getRoot')
            ->willReturn($formRoot);

        $this->fieldForm->method('getRoot')
            ->willReturn($userEditForm);
    }

    public function testMapFieldValueFormNoLanguageCode()
    {
        $mapper = new UserAccountFieldValueFormMapper();

        $fieldDefinition = new FieldDefinition(['names' => []]);

        $this->data->expects($this->once())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $mapper->mapFieldValueForm($this->fieldForm, $this->data);
    }

    public function testMapFieldValueFormWithLanguageCode()
    {
        $mapper = new UserAccountFieldValueFormMapper();

        $fieldDefinition = new FieldDefinition(['names' => ['eng-GB' => 'foo']]);

        $this->data->expects($this->once())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $mapper->mapFieldValueForm($this->fieldForm, $this->data);
    }
}
