<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldDefinition;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use eZ\Publish\Core\Repository\Strategy\ContentThumbnail\Field\ContentFieldStrategy;
use eZ\Publish\Core\Repository\Strategy\ContentThumbnail\FirstMatchingFieldStrategy;
use eZ\Publish\Core\Repository\Strategy\ContentThumbnail\ThumbnailChainStrategy;
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
    /**
     * @var \eZ\Publish\Core\Repository\Strategy\ContentThumbnail\Field\ContentFieldStrategy
     */
    private $contentFieldStrategy;

    public function __construct(FieldTypeFormMapperDispatcherInterface $fieldTypeMapperDispatcher, FieldTypeService $fieldTypeService, ContentFieldStrategy $contentFieldStrategy)
    {
        $this->fieldTypeMapperDispatcher = $fieldTypeMapperDispatcher;
        $this->fieldTypeService = $fieldTypeService;
        $this->contentFieldStrategy = $contentFieldStrategy;
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
                'mainLanguageCode' => null,
                'hasThumbnailStrategy' => false
            ])
            ->setDefined(['mainLanguageCode', 'hasThumbnailStrategy'])
            ->setAllowedTypes('mainLanguageCode', ['null', 'string'])
            ->setRequired(['languageCode']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fieldsGroups = [];
        if (isset($this->groupsList)) {
            $fieldsGroups = array_flip($this->groupsList->getGroups());
        }

        $translatablePropertyTransformer = new TranslatablePropertyTransformer($options['languageCode']);
        $isTranslation = $options['languageCode'] !== $options['mainLanguageCode'];
        $hasThumbnailStrategy = $options['hasThumbnailStrategy'];

        $builder
            ->add(
                $builder->create('name',
                    TextType::class,
                    [
                        'property_path' => 'names',
                        'label' => 'field_definition.name',
                    ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add(
                'identifier',
                TextType::class,
                [
                    'label' => 'field_definition.identifier',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                $builder->create('description', TextType::class, [
                    'property_path' => 'descriptions',
                    'required' => false,
                    'label' => 'field_definition.description',
                ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('isRequired', CheckboxType::class, [
                'required' => false,
                'label' => 'field_definition.is_required',
                'disabled' => $isTranslation,
            ])
            ->add('isTranslatable', CheckboxType::class, [
                'required' => false,
                'label' => 'field_definition.is_translatable',
                'disabled' => $isTranslation,
            ])
            ->add(
                'fieldGroup', ChoiceType::class, [
                    'choices' => $fieldsGroups,
                    'required' => false,
                    'label' => 'field_definition.field_group',
                    'disabled' => $isTranslation,
                ]
            )
            ->add('position', IntegerType::class, [
                'label' => 'field_definition.position',
                'disabled' => $isTranslation,
            ])
            ->add('selected', CheckboxType::class, [
                'required' => false,
                'mapped' => false,
                'disabled' => $isTranslation,
            ]);

        // Hook on form generation for specific FieldType needs
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \EzSystems\RepositoryForms\Data\FieldDefinitionData $data */
            $data = $event->getData();
            $form = $event->getForm();
            $fieldTypeIdentifier = $data->getFieldTypeIdentifier();
            $fieldType = $this->fieldTypeService->getFieldType($fieldTypeIdentifier);
            $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
            // isSearchable field should be present only if the FieldType allows it.
            $form->add('isSearchable', CheckboxType::class, [
                'required' => false,
                'disabled' => !$fieldType->isSearchable() || $isTranslation,
                'label' => 'field_definition.is_searchable',
            ]);

            $form->add('isThumbnail', CheckboxType::class, [
                'required' => false,
                'label' => 'field_definition.is_thumbnail',
                'disabled' => $isTranslation || !$this->contentFieldStrategy->hasStrategy($fieldTypeIdentifier),
            ]);

            // Let fieldType mappers do their jobs to complete the form.
            $this->fieldTypeMapperDispatcher->map($form, $data);
        });
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_fielddefinition_update';
    }
}
