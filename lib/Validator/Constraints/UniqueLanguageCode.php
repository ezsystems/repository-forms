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
class UniqueLanguageCode extends Constraint
{
    /**
     * %language_code% placeholder is passed.
     *
     * @var string
     */
    public $message = 'ez.language.code.unique';

    public function validatedBy()
    {
        return 'ezrepoforms.validator.unique_language_code';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
