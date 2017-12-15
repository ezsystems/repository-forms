<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Validator\Constraints;

use EzSystems\RepositoryForms\Validator\Constraints\UniqueURL;
use PHPUnit\Framework\TestCase;

class UniqueURLTest extends TestCase
{
    /** @var \EzSystems\RepositoryForms\Validator\Constraints\UniqueURL */
    private $constraint;

    protected function setUp()
    {
        $this->constraint = new UniqueURL();
    }

    public function testConstruct()
    {
        $this->assertSame('ez.url.unique', $this->constraint->message);
    }

    public function testValidatedBy()
    {
        $this->assertSame('ezrepoforms.validator.unique_url', $this->constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $this->assertSame(UniqueURL::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }
}
