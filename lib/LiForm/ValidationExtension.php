<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\RepositoryForms\LiForm;

use Symfony\Component\Validator\Constraints\Length;

/**
 * Liform extension to handle validation constraints.
 */
class ValidationExtension
{
    public function apply($form, $schema)
    {
        $constraints = $form->getConfig()->getOption('constraints');
        foreach ($constraints as $constraint) {
            if ($constraint instanceof Length) {
                if ($constraint->min) {
                    $schema['minLength'] = $constraint->min;
                }
                if ($constraint->max) {
                    $schema['maxLength'] = $constraint->max;
                }
                continue;
            }
        }

        return $schema;
    }
}
