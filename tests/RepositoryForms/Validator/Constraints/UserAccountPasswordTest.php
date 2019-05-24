<?php

declare(strict_types=1);

namespace EzSystems\RepositoryForms\Tests\Validator\Constraints;

use EzSystems\RepositoryForms\Validator\Constraints\UserAccountPassword;
use EzSystems\RepositoryForms\Validator\Constraints\UserAccountPasswordValidator;
use PHPUnit\Framework\TestCase;

class UserAccountPasswordTest extends TestCase
{
    /** @var \EzSystems\RepositoryForms\Validator\Constraints\Password */
    private $constraint;

    protected function setUp()
    {
        $this->constraint = new UserAccountPassword();
    }

    public function testConstruct()
    {
        $this->assertSame('ez.user.password.invalid', $this->constraint->message);
    }

    public function testValidatedBy()
    {
        $this->assertSame(UserAccountPasswordValidator::class, $this->constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $this->assertSame([UserAccountPassword::CLASS_CONSTRAINT, UserAccountPassword::PROPERTY_CONSTRAINT], $this->constraint->getTargets());
    }
}
