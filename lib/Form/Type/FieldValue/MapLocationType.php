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

/**
 * Combined entry type for ezgmaplocation.
 */
class MapLocationType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'ezrepoforms_fieldtype_ezgmaplocation';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'latitude',
                NumberType::class,
                [
                    'label' => 'content.field_type.ezgmaplocation.latitude',
                    'required' => $options['required'],
                    'attr' => [
                        'min' => -90,
                        'max' => 90,
                        'step' => 'any',
                    ],
                ]
            )
            ->add(
                'longitude',
                NumberType::class,
                [
                    'label' => 'content.field_type.ezgmaplocation.longitude',
                    'required' => $options['required'],
                    'attr' => [
                        'min' => -90,
                        'max' => 90,
                        'step' => 'any',
                    ],
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'label' => 'content.field_type.ezgmaplocation.address',
                    'required' => false,
                    'empty_data' => '',
                ]
            );
    }
}
