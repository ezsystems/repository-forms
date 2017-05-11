<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\RepositoryForms\Form\Type\FieldValue;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TextFieldType extends AbstractFieldType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $textOptions = ['label' => $options['field_definition']->getName('eng-GB')];
        if (isset($options['properties_constraints']['text'])) {
            $textOptions['constraints'] = $options['properties_constraints']['text'];
        }

        $builder->add('text', TextType::class, $textOptions);
    }
}
