<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Tests\Validator\Constraints;

use EzSystems\RepositoryForms\Validator\Constraints\FieldSettings;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Validator\Constraint;

class FieldSettingsTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $constraint = new FieldSettings();
        self::assertSame('ez.field_definition.field_settings', $constraint->message);
    }

    public function testValidatedBy()
    {
        $constraint = new FieldSettings();
        self::assertSame('ezrepoforms.validator.field_settings', $constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $constraint = new FieldSettings();
        self::assertSame(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}
