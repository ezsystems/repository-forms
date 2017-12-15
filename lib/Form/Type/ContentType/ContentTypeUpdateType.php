<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\ContentType;

use EzSystems\RepositoryForms\Form\DataTransformer\TranslatablePropertyTransformer;
use EzSystems\RepositoryForms\Form\Type\FieldDefinition\FieldDefinitionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for ContentType update.
 */
class ContentTypeUpdateType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_contenttype_update';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'EzSystems\RepositoryForms\Data\ContentTypeData',
                'translation_domain' => 'ezrepoforms_content_type',
            ])
            ->setRequired(['languageCode']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $hasFieldDefinition = count($options['data']->fieldDefinitionsData) > 0;
        $translatablePropertyTransformer = new TranslatablePropertyTransformer($options['languageCode']);
        $builder
            ->add(
                $builder
                    ->create('name', TextType::class, ['property_path' => 'names', 'label' => 'content_type.name'])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('identifier', TextType::class, ['label' => 'content_type.identifier'])
            ->add(
                $builder
                    ->create('description', TextType::class, [
                        'property_path' => 'descriptions',
                        'required' => false,
                        'label' => 'content_type.description',
                    ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('nameSchema', TextType::class, ['required' => false, 'label' => 'content_type.name_schema'])
            ->add('urlAliasSchema', TextType::class, ['required' => false, 'label' => 'content_type.url_alias_schema', 'empty_data' => false])
            ->add('isContainer', CheckboxType::class, ['required' => false, 'label' => 'content_type.is_container'])
            ->add('defaultSortField', SortFieldChoiceType::class, [
                'label' => 'content_type.default_sort_field',
            ])
            ->add('defaultSortOrder', SortOrderChoiceType::class, [
                'label' => 'content_type.default_sort_order',
            ])
            ->add('defaultAlwaysAvailable', CheckboxType::class, [
                'required' => false,
                'label' => 'content_type.default_always_available',
            ])
            ->add('fieldDefinitionsData', CollectionType::class, [
                'entry_type' => FieldDefinitionType::class,
                'entry_options' => ['languageCode' => $options['languageCode']],
                'label' => 'content_type.field_definitions_data',
            ])
            ->add('fieldTypeSelection', FieldTypeChoiceType::class, [
                'mapped' => false,
                'label' => 'content_type.field_type_selection',
            ])
            ->add('addFieldDefinition', SubmitType::class, ['label' => 'content_type.add_field_definition'])
            ->add('removeFieldDefinition', SubmitType::class, [
                'label' => 'content_type.remove_field_definitions',
                'disabled' => !$hasFieldDefinition,
            ])
            ->add('saveContentType', SubmitType::class, ['label' => 'content_type.save'])
            ->add('removeDraft', SubmitType::class, ['label' => 'content_type.remove_draft', 'validation_groups' => false])
            ->add('publishContentType', SubmitType::class, [
                'label' => 'content_type.publish',
                'disabled' => !$hasFieldDefinition,
            ]);
    }
}
