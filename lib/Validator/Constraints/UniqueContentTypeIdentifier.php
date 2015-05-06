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
class UniqueContentTypeIdentifier extends Constraint
{
    /**
     * %identifier% placeholder is passed.
     *
     * @var string
     */
    public $message = 'ez.content_type.identifier.unique';

    public function validatedBy()
    {
        return 'ezrepoforms.validator.unique_content_type_identifier';
    }
}
