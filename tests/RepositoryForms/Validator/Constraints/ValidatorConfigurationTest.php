<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Tests\Validator\Constraints;

use EzSystems\RepositoryForms\Validator\Constraints\ValidatorConfiguration;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Validator\Constraint;

class ValidatorConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $constraint = new ValidatorConfiguration();
        self::assertSame('ez.field_definition.validator_configuration', $constraint->message);
    }

    public function testValidatedBy()
    {
        $constraint = new ValidatorConfiguration();
        self::assertSame('ezrepoforms.validator.validator_configuration', $constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $constraint = new ValidatorConfiguration();
        self::assertSame(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}
