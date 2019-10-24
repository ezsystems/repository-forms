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
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperDispatcher;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperDispatcherInterface;
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
     * @var \EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $fieldValueMapperMock;

    protected function setUp(): void
    {
        $this->dispatcher = new FieldTypeFormMapperDispatcher();

        $this->fieldValueMapperMock = $this->createMock(FieldValueFormMapperInterface::class);
        $this->dispatcher->addMapper($this->fieldValueMapperMock, 'first_type');
    }

    public function testMapFieldValue()
    {
        $data = new FieldData([
            'field' => new Field(['fieldDefIdentifier' => 'first_type']),
            'fieldDefinition' => new FieldDefinition(['fieldTypeIdentifier' => 'first_type']),
        ]);

        $formMock = $this->createMock(FormInterface::class);

        $this->fieldValueMapperMock
            ->expects($this->once())
            ->method('mapFieldValueForm')
            ->with($formMock, $data);

        $this->dispatcher->map($formMock, $data);
    }
}
