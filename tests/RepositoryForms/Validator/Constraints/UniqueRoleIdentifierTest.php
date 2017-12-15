<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Validator\Constraints;

use EzSystems\RepositoryForms\Validator\Constraints\UniqueRoleIdentifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

class UniqueRoleIdentifierTest extends TestCase
{
    public function testConstruct()
    {
        $constraint = new UniqueRoleIdentifier();
        self::assertSame('ez.role.identifier.unique', $constraint->message);
    }

    public function testValidateBy()
    {
        $constraint = new UniqueRoleIdentifier();
        self::assertSame('ezrepoforms.validator.unique_role_identifier', $constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $constraint = new UniqueRoleIdentifier();
        self::assertSame(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}
