<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueFieldDefinitionIdentifier extends Constraint
{
    /**
     * %identifier% placeholder is passed.
     *
     * @var string
     */
    public $message = 'ez.field_definition.identifier.unique';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
