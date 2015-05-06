<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Tests\Validator\Constraints;

use EzSystems\RepositoryForms\Data\ContentTypeData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\Validator\Constraints\UniqueFieldDefinitionIdentifier;
use EzSystems\RepositoryForms\Validator\Constraints\UniqueFieldDefinitionIdentifierValidator;
use PHPUnit_Framework_TestCase;

class UniqueFieldDefinitionIdentifierValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $executionContext;

    /**
     * @var UniqueFieldDefinitionIdentifierValidator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();
        $this->executionContext = $this->getMock('\Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->validator = new UniqueFieldDefinitionIdentifierValidator();
        $this->validator->initialize($this->executionContext);
    }

    public function testNotFieldDefinitionData()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('foo', new UniqueFieldDefinitionIdentifier());
    }

    public function testValid()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $contentTypeData = new ContentTypeData();
        $fieldDefData1 = new FieldDefinitionData(['identifier' => 'foo', 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData1);
        $fieldDefData2 = new FieldDefinitionData(['identifier' => 'bar', 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData2);
        $fieldDefData3 = new FieldDefinitionData(['identifier' => 'baz', 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData3);

        $this->validator->validate($fieldDefData1, new UniqueFieldDefinitionIdentifier());
    }

    public function testInvalid()
    {
        $identifier = 'foo';
        $constraint = new UniqueFieldDefinitionIdentifier();
        $constraintViolationBuilder = $this->getMock('\Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');
        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('atPath')
            ->with('identifier')
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('%identifier%', $identifier)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('addViolation');

        $contentTypeData = new ContentTypeData();
        $fieldDefData1 = new FieldDefinitionData(['identifier' => $identifier, 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData1);
        $fieldDefData2 = new FieldDefinitionData(['identifier' => 'bar', 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData2);
        $fieldDefData3 = new FieldDefinitionData(['identifier' => $identifier, 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData3);

        $this->validator->validate($fieldDefData1, $constraint);
    }
}
