<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Tests\Validator\Constraints;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use EzSystems\RepositoryForms\Validator\Constraints\UniqueContentTypeIdentifier;
use EzSystems\RepositoryForms\Validator\Constraints\UniqueContentTypeIdentifierValidator;
use PHPUnit_Framework_TestCase;

class UniqueContentTypeIdentifierValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contentTypeService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $executionContext;

    /**
     * @var UniqueContentTypeIdentifierValidator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();
        $this->contentTypeService = $this->getMock('\eZ\Publish\API\Repository\ContentTypeService');
        $this->executionContext = $this->getMock('\Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->validator = new UniqueContentTypeIdentifierValidator($this->contentTypeService);
        $this->validator->initialize($this->executionContext);
    }

    public function testValid()
    {
        $identifier = 'foo_identifier';
        $this->contentTypeService
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($identifier)
            ->willThrowException(new NotFoundException('foo', 'bar'));
        $this->executionContext
            ->expects($this->never())
            ->method('buildVioloation');

        $this->validator->validate($identifier, new UniqueContentTypeIdentifier());
    }

    public function testInvalid()
    {
        $identifier = 'foo_identifier';
        $constraint = new UniqueContentTypeIdentifier();
        $constraintViolationBuilder = $this->getMock('\Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');
        $this->contentTypeService
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($identifier)
            ->willReturn($this->getMockForAbstractClass('\eZ\Publish\API\Repository\Values\ContentType\ContentType'));
        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('%identifier%', $identifier)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($identifier, $constraint);
    }
}
