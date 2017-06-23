<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelationListFormMapper extends AbstractRelationFormMapper
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add('selectionDefaultLocation', HiddenType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[selectionDefaultLocation]',
                'label' => 'field_definition.ezobjectrelationlist.selection_default_location',
            ])
            ->add('selectionContentTypes', ChoiceType::class, [
                'choices' => $this->getContentTypeHash(),
                'choices_as_values' => true,
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'property_path' => 'fieldSettings[selectionContentTypes]',
                'label' => 'field_definition.ezobjectrelationlist.selection_content_types',
            ]);
    }

    /**
     * Fake method to set the translation domain for the extractor.
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }
}
