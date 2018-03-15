<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueUserLogin extends Constraint
{
    /**
     * %login% placeholder is passed.
     *
     * @var string
     */
    public $message = 'ez.user.login.unique';

    public function validatedBy()
    {
        return 'ezrepoforms.validator.unique_user_login';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
