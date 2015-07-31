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
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperRegistryInterface;
use EzSystems\RepositoryForms\Form\DataTransformer\TranslatablePropertyTransformer;
use Symfony\Component\Form\AbstractType;
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
     * @var FieldTypeFormMapperRegistryInterface
     */
    private $fieldTypeMapperRegistry;

    /**
     * @var FieldTypeService
     */
    private $fieldTypeService;

    public function __construct(FieldTypeFormMapperRegistryInterface $fieldTypeMapperRegistry, FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeMapperRegistry = $fieldTypeMapperRegistry;
        $this->fieldTypeService = $fieldTypeService;
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
        $translatablePropertyTransformer = new TranslatablePropertyTransformer($options['languageCode']);
        $builder
            ->add(
                $builder->create('name', 'text', ['property_path' => 'names', 'label' => 'field_definition.name'])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('identifier', 'text', ['label' => 'field_definition.identifier'])
            ->add(
                $builder->create('description', 'text', [
                    'property_path' => 'descriptions',
                    'required' => false,
                    'label' => 'field_definition.description',
                ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('isRequired', 'checkbox', ['required' => false, 'label' => 'field_definition.is_required'])
            ->add('isTranslatable', 'checkbox', ['required' => false, 'label' => 'field_definition.is_translatable'])
            ->add('fieldGroup', 'choice', ['choices' => []], ['required' => false, 'label' => 'field_definition.field_group'])
            ->add('position', 'integer', ['label' => 'field_definition.position'])
            ->add('selected', 'checkbox', ['required' => false, 'mapped' => false]);

        // Hook on form generation for specific FieldType needs
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \EzSystems\RepositoryForms\Data\FieldDefinitionData $data */
            $data = $event->getData();
            $form = $event->getForm();
            $fieldTypeIdentifier = $data->getFieldTypeIdentifier();
            $fieldType = $this->fieldTypeService->getFieldType($fieldTypeIdentifier);
            // isSearchable field should be present only if the FieldType allows it.
            $form->add('isSearchable', 'checkbox', [
                'required' => false,
                'disabled' => !$fieldType->isSearchable(),
                'label' => 'field_definition.is_searchable',
            ]);

            // Let fieldType mappers do their jobs to complete the form.
            if ($this->fieldTypeMapperRegistry->hasMapper($fieldTypeIdentifier)) {
                $mapper = $this->fieldTypeMapperRegistry->getMapper($fieldTypeIdentifier);
                $mapper->mapFieldDefinitionForm($form, $data);
            }
        });
    }

    public function getName()
    {
        return 'ezrepoforms_fielddefinition_update';
    }
}
