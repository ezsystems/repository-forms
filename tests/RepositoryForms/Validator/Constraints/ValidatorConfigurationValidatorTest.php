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
use EzSystems\RepositoryForms\Validator\Constraints\ValidatorConfiguration;
use EzSystems\RepositoryForms\Validator\Constraints\ValidatorConfigurationValidator;
use PHPUnit_Framework_TestCase;

class ValidatorConfigurationValidatorTest extends PHPUnit_Framework_TestCase
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
     * @var ValidatorConfigurationValidator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();
        $this->executionContext = $this->getMock('\Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->fieldTypeService = $this->getMock('\eZ\Publish\API\Repository\FieldTypeService');
        $this->validator = new ValidatorConfigurationValidator($this->fieldTypeService);
        $this->validator->initialize($this->executionContext);
    }

    public function testNotFieldDefinitionData()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('foo', new ValidatorConfiguration());
    }

    public function testValid()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $fieldTypeIdentifier = 'ezstring';
        $fieldDefinition = new FieldDefinition(['fieldTypeIdentifier' => $fieldTypeIdentifier]);
        $validatorConfiguration = ['foo' => 'bar'];
        $fieldDefData = new FieldDefinitionData(['identifier' => 'foo', 'fieldDefinition' => $fieldDefinition, 'validatorConfiguration' => $validatorConfiguration]);
        $fieldType = $this->getMock('\eZ\Publish\API\Repository\FieldType');
        $this->fieldTypeService
            ->expects($this->once())
            ->method('getFieldType')
            ->with($fieldTypeIdentifier)
            ->willReturn($fieldType);
        $fieldType
            ->expects($this->once())
            ->method('validateValidatorConfiguration')
            ->with($validatorConfiguration)
            ->willReturn([]);

        $this->validator->validate($fieldDefData, new ValidatorConfiguration());
    }

    public function testInvalid()
    {
        $fieldTypeIdentifier = 'ezstring';
        $fieldDefinition = new FieldDefinition(['fieldTypeIdentifier' => $fieldTypeIdentifier]);
        $validatorConfiguration = ['foo' => 'bar'];
        $fieldDefData = new FieldDefinitionData(['identifier' => 'foo', 'fieldDefinition' => $fieldDefinition, 'validatorConfiguration' => $validatorConfiguration]);
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
            ->method('validateValidatorConfiguration')
            ->with($validatorConfiguration)
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

        $this->validator->validate($fieldDefData, new ValidatorConfiguration());
    }
}
