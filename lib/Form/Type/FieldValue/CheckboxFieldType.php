<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\RepositoryForms\Form\Type\FieldValue;

use eZ\Publish\Core\FieldType\Checkbox\Value;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class CheckboxFieldType extends CheckboxType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                function (Value $checkbox) {
                    return $checkbox->bool;
                },
                function ($bool) {
                    return new Value($bool);
                }
            )
        );
    }
}
