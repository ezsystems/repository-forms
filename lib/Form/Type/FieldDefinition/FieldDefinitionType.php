<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldDefinition;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperDispatcherInterface;
use EzSystems\RepositoryForms\Form\DataTransformer\TranslatablePropertyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for FieldDefinition update.
 */
class FieldDefinitionType extends AbstractType
{
    /**
     * @var \EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperDispatcherInterface
     */
    private $fieldTypeMapperDispatcher;

    /**
     * @var FieldTypeService
     */
    private $fieldTypeService;

    /**
     * @var FieldsGroupsList
     */
    private $groupsList;

    public function __construct(FieldTypeFormMapperDispatcherInterface $fieldTypeMapperDispatcher, FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeMapperDispatcher = $fieldTypeMapperDispatcher;
        $this->fieldTypeService = $fieldTypeService;
    }

    public function setGroupsList(FieldsGroupsList $groupsList)
    {
        $this->groupsList = $groupsList;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'EzSystems\RepositoryForms\Data\FieldDefinitionData',
                'translation_domain' => 'ezrepoforms_content_type',
            ])
            ->setRequired(['languageCode']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fieldsGroups = [];
        if (isset($this->groupsList)) {
            $fieldsGroups = array_flip($this->groupsList->getGroups());
        }

        $translatablePropertyTransformer = new TranslatablePropertyTransformer($options['languageCode']);
        $builder
            ->add(
                $builder->create('name', TextType::class, ['property_path' => 'names', 'label' => 'field_definition.name'])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('identifier', TextType::class, ['label' => 'field_definition.identifier'])
            ->add(
                $builder->create('description', TextType::class, [
                    'property_path' => 'descriptions',
                    'required' => false,
                    'label' => 'field_definition.description',
                ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('isRequired', CheckboxType::class, ['required' => false, 'label' => 'field_definition.is_required'])
            ->add('isTranslatable', CheckboxType::class, ['required' => false, 'label' => 'field_definition.is_translatable'])
            ->add(
                'fieldGroup',
                ChoiceType::class, [
                    'choices' => $fieldsGroups,
                    'choices_as_values' => true,
                    'required' => false,
                    'label' => 'field_definition.field_group',
                ]
            )
            ->add('position', IntegerType::class, ['label' => 'field_definition.position'])
            ->add('selected', CheckboxType::class, ['required' => false, 'mapped' => false]);

        // Hook on form generation for specific FieldType needs
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \EzSystems\RepositoryForms\Data\FieldDefinitionData $data */
            $data = $event->getData();
            $form = $event->getForm();
            $fieldTypeIdentifier = $data->getFieldTypeIdentifier();
            $fieldType = $this->fieldTypeService->getFieldType($fieldTypeIdentifier);
            // isSearchable field should be present only if the FieldType allows it.
            $form->add('isSearchable', CheckboxType::class, [
                'required' => false,
                'disabled' => !$fieldType->isSearchable(),
                'label' => 'field_definition.is_searchable',
            ]);

            // Let fieldType mappers do their jobs to complete the form.
            $this->fieldTypeMapperDispatcher->map($form, $data);
        });
    }

    public function getName()
    {
        return 'ezrepoforms_fielddefinition_update';
    }
}
