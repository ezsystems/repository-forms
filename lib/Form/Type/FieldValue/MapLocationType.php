<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\RepositoryForms\Form\Type\FieldValue;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapLocationType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_fieldtype_maplocation';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'latitude',
                NumberType::class,
                ['label' => 'Latitude', 'scale' => 5, 'property_path' => 'latitude', 'required' => $options['required']])
            ->add(
                'longitude',
                NumberType::class,
                ['label' => 'Longitude', 'scale' => 5, 'property_path' => 'longitude', 'required' => $options['required']])
            ->add(
                'address',
                TextType::class,
                ['label' => 'Address', 'empty_data' => '', 'property_path' => 'address', 'required' => $options['required']]);
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefaults([
            'required' => false,
        ]);
    }
}
