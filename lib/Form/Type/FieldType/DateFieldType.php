<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldType;

use EzSystems\RepositoryForms\FieldType\DataTransformer\DateValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezdate field type.
 */
class DateFieldType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezdate';
    }

    public function getParent()
    {
        return DateType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new DateValueTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'input' => 'datetime',
            'widget' => 'single_text',
            'html5' => false,
        ]);
    }
}
