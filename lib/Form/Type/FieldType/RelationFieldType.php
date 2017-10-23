<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldType;

use EzSystems\RepositoryForms\FieldType\DataTransformer\RelationValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezobjectrelation field type.
 */
class RelationFieldType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezobjectrelation';
    }

    public function getParent()
    {
        return IntegerType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new RelationValueTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'min' => 1,
                'step' => 1,
            ],
        ]);
    }
}
