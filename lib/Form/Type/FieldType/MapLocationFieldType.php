<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldType;

use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\RepositoryForms\FieldType\DataTransformer\FieldValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form Type representing ezgmaplocation field type.
 */
class MapLocationFieldType extends AbstractType
{
    /** @var FieldTypeService */
    protected $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezgmaplocation';
    }

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
                ]
            )
            ->addModelTransformer(new FieldValueTransformer($this->fieldTypeService->getFieldType('ezgmaplocation')));
    }
}
