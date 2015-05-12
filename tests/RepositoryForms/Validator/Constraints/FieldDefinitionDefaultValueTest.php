<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Tests\Validator\Constraints;

use EzSystems\RepositoryForms\Validator\Constraints\FieldDefinitionDefaultValue;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Validator\Constraint;

class FieldDefinitionDefaultValueTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $constraint = new FieldDefinitionDefaultValue();
        self::assertSame('ez.field_definition.default_field_value', $constraint->message);
    }

    public function testValidatedBy()
    {
        $constraint = new FieldDefinitionDefaultValue();
        self::assertSame('ezrepoforms.validator.default_field_value', $constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $constraint = new FieldDefinitionDefaultValue();
        self::assertSame(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}
