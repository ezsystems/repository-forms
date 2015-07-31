<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\DataTransformer\CountryValueTransformer;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormInterface;

class CountryFormMapper implements FieldTypeFormMapperInterface
{
    /**
     * @var array Array of countries from ezpublish.fieldType.ezcountry.data
     */
    protected $countriesInfo;

    /**
     * @param array $countriesInfo
     */
    public function __construct(array $countriesInfo)
    {
        $this->countriesInfo = $countriesInfo;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
                'isMultiple',
                'checkbox', [
                    'required' => false,
                    'property_path' => 'fieldSettings[isMultiple]',
                    'label' => 'field_definition.ezcountry.is_multiple',
                ]
            )
            ->add(
                // Creating from FormBuilder as we need to add a DataTransformer.
                $fieldDefinitionForm->getConfig()->getFormFactory()->createBuilder()
                    ->create(
                        'defaultValue',
                        'choice', [
                            'choice_list' => new ChoiceList(
                                array_map(
                                    function ($country) {
                                        return $country['Alpha2'];
                                    },
                                    $this->countriesInfo
                                ),
                                array_map(
                                    function ($country) {
                                        return $country['Name'];
                                    },
                                    $this->countriesInfo
                                )
                            ),
                            'multiple' => true,
                            'expanded' => false,
                            'required' => false,
                            'label' => 'field_definition.ezcountry.default_value',
                        ]
                    )
                    ->addModelTransformer(new CountryValueTransformer($this->countriesInfo))
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)->getForm()
            );
    }
}
