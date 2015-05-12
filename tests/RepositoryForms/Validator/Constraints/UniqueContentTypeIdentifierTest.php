<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Tests\Validator\Constraints;

use EzSystems\RepositoryForms\Validator\Constraints\UniqueContentTypeIdentifier;
use PHPUnit_Framework_TestCase;

class UniqueContentTypeIdentifierTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $constraint = new UniqueContentTypeIdentifier();
        self::assertSame('ez.content_type.identifier.unique', $constraint->message);
    }

    public function testValidatedBy()
    {
        $constraint = new UniqueContentTypeIdentifier();
        self::assertSame('ezrepoforms.validator.unique_content_type_identifier', $constraint->validatedBy());
    }
}
