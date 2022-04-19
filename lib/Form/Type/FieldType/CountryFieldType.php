<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldType;

use EzSystems\RepositoryForms\FieldType\DataTransformer\MultipleCountryValueTransformer;
use EzSystems\RepositoryForms\FieldType\DataTransformer\SingleCountryValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezcountry field type.
 */
class CountryFieldType extends AbstractType
{
    /** @var array */
    protected $countriesInfo;

    public function __construct(array $countriesInfo)
    {
        $this->countriesInfo = $countriesInfo;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezcountry';
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            $options['multiple']
                ? new MultipleCountryValueTransformer($this->countriesInfo)
                : new SingleCountryValueTransformer($this->countriesInfo)
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'expanded' => false,
            'choices' => $this->getCountryChoices($this->countriesInfo),
        ]);
    }

    private function getCountryChoices(array $countriesInfo)
    {
        $choices = [];
        foreach ($countriesInfo as $country) {
            $choices[$country['Name']] = $country['Alpha2'];
        }

        return $choices;
    }
}
