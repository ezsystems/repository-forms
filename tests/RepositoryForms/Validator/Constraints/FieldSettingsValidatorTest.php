<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Tests\Validator\Constraints;

use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\Validator\Constraints\FieldSettings;
use EzSystems\RepositoryForms\Validator\Constraints\FieldSettingsValidator;
use PHPUnit_Framework_TestCase;

class FieldSettingsValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $executionContext;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $fieldTypeService;

    /**
     * @var FieldSettingsValidator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();
        $this->executionContext = $this->getMock('\Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->fieldTypeService = $this->getMock('\eZ\Publish\API\Repository\FieldTypeService');
        $this->validator = new FieldSettingsValidator($this->fieldTypeService);
        $this->validator->initialize($this->executionContext);
    }

    public function testNotFieldDefinitionData()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('foo', new FieldSettings());
    }

    public function testValid()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $fieldTypeIdentifier = 'ezstring';
        $fieldDefinition = new FieldDefinition(['fieldTypeIdentifier' => $fieldTypeIdentifier]);
        $fieldSettings = ['foo' => 'bar'];
        $fieldDefData = new FieldDefinitionData(['identifier' => 'foo', 'fieldDefinition' => $fieldDefinition, 'fieldSettings' => $fieldSettings]);
        $fieldType = $this->getMock('\eZ\Publish\API\Repository\FieldType');
        $this->fieldTypeService
            ->expects($this->once())
            ->method('getFieldType')
            ->with($fieldTypeIdentifier)
            ->willReturn($fieldType);
        $fieldType
            ->expects($this->once())
            ->method('validateFieldSettings')
            ->with($fieldSettings)
            ->willReturn([]);

        $this->validator->validate($fieldDefData, new FieldSettings());
    }

    public function testInvalid()
    {
        $fieldTypeIdentifier = 'ezstring';
        $fieldDefinition = new FieldDefinition(['fieldTypeIdentifier' => $fieldTypeIdentifier]);
        $fieldSettings = ['foo' => 'bar'];
        $fieldDefData = new FieldDefinitionData(['identifier' => 'foo', 'fieldDefinition' => $fieldDefinition, 'fieldSettings' => $fieldSettings]);
        $fieldType = $this->getMock('\eZ\Publish\API\Repository\FieldType');
        $this->fieldTypeService
            ->expects($this->once())
            ->method('getFieldType')
            ->with($fieldTypeIdentifier)
            ->willReturn($fieldType);

        $errorParameter = 'bar';
        $errorMessage = 'error';
        $fieldType
            ->expects($this->once())
            ->method('validateFieldSettings')
            ->with($fieldSettings)
            ->willReturn([new ValidationError($errorMessage, null, ['foo' => $errorParameter])]);

        $constraintViolationBuilder = $this->getMock('\Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');
        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->willReturn($constraintViolationBuilder);
        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with($errorMessage)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('%foo%', $errorParameter)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($fieldDefData, new FieldSettings());
    }
}
