<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\FieldType;

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\ContentTypeData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperDispatcher;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperDispatcherInterface;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

class FieldTypeFormMapperDispatcherTest extends TestCase
{
    /**
     * @var FieldTypeFormMapperDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fieldDefinitionMapperMock;

    /**
     * @var \EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fieldValueMapperMock;

    public function setUp()
    {
        $this->dispatcher = new FieldTypeFormMapperDispatcher();

        // Should be improved to test with a value + definition mapper (this is what repo-forms uses)
        $this->fieldDefinitionMapperMock = $this->createMock(FieldDefinitionFormMapperInterface::class);
        $this->fieldValueMapperMock = $this->createMock(FieldValueFormMapperInterface::class);
        $this->dispatcher->addMapper($this->fieldDefinitionMapperMock, 'first_type');
        $this->dispatcher->addMapper($this->fieldValueMapperMock, 'second_type');
    }

    public function testMapFieldDefinition()
    {
        $data = new FieldDefinitionData([
            'fieldDefinition' => new FieldDefinition(['fieldTypeIdentifier' => 'first_type']),
            'contentTypeData' => new ContentTypeData(),
        ]);

        $formMock = $this->createMock(FormInterface::class);

        $this->fieldValueMapperMock
            ->expects($this->never())
            ->method('mapFieldValueForm');

        $this->fieldDefinitionMapperMock
            ->expects($this->once())
            ->method('mapFieldDefinitionForm')
            ->with($formMock, $data);

        $this->dispatcher->map($formMock, $data);
    }

    public function testMapFieldValue()
    {
        $data = new FieldData([
            'field' => new Field(['fieldDefIdentifier' => 'second_type']),
            'fieldDefinition' => new FieldDefinition(['fieldTypeIdentifier' => 'second_type']),
        ]);

        $formMock = $this->createMock(FormInterface::class);

        $this->fieldValueMapperMock
            ->expects($this->once())
            ->method('mapFieldValueForm')
            ->with($formMock, $data);

        $this->fieldDefinitionMapperMock
            ->expects($this->never())
            ->method('mapFieldDefinitionForm');

        $this->dispatcher->map($formMock, $data);
    }
}
