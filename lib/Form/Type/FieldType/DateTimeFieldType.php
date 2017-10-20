<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldType;

use EzSystems\RepositoryForms\FieldType\DataTransformer\DateTimeValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezdatetime field type.
 */
class DateTimeFieldType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezdatetime';
    }

    public function getParent()
    {
        return DateTimeType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAttributes($this->getAttributes($options))
            ->addModelTransformer(new DateTimeValueTransformer());
    }

    private function getAttributes(array $options)
    {
        $attributes = [];

        if ($options['with_seconds']) {
            $attributes['step'] = 1;
        }

        return $attributes;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'input' => 'datetime',
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
            'html5' => false,
        ]);
    }
}
