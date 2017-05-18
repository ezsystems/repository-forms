<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Type\ContentType;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Base\Container\ApiLoader\FieldTypeCollectionFactory;
use EzSystems\RepositoryForms\Form\DataTransformer\TranslatablePropertyTransformer;
use EzSystems\RepositoryForms\Form\Type\FieldDefinition\FieldDefinitionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Form type for ContentType update.
 */
class ContentTypeUpdateType extends AbstractType
{
    /**
     * @var FieldTypeCollectionFactory
     */
    private $fieldTypeCollectionFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(FieldTypeCollectionFactory $fieldTypeCollectionFactory, TranslatorInterface $translator)
    {
        $this->fieldTypeCollectionFactory = $fieldTypeCollectionFactory;
        $this->translator = $translator;
    }

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
            ->add('urlAliasSchema', TextType::class, ['required' => false, 'label' => 'content_type.url_alias_schema'])
            ->add('isContainer', CheckboxType::class, ['required' => false, 'label' => 'content_type.is_container'])
            ->add('defaultSortField', ChoiceType::class, [
                'choices' => [
                    $this->translator->trans(/** @Ignore */'content_type.sort_field.' . Location::SORT_FIELD_NAME, [], 'ezrepoforms_content_type') => Location::SORT_FIELD_NAME,
                    $this->translator->trans(/** @Ignore */'content_type.sort_field.' . Location::SORT_FIELD_CLASS_NAME, [], 'ezrepoforms_content_type') => Location::SORT_FIELD_CLASS_NAME,
                    $this->translator->trans(/** @Ignore */'content_type.sort_field.' . Location::SORT_FIELD_CLASS_IDENTIFIER, [], 'ezrepoforms_content_type') => Location::SORT_FIELD_CLASS_IDENTIFIER,
                    $this->translator->trans(/** @Ignore */'content_type.sort_field.' . Location::SORT_FIELD_DEPTH, [], 'ezrepoforms_content_type') => Location::SORT_FIELD_DEPTH,
                    $this->translator->trans(/** @Ignore */'content_type.sort_field.' . Location::SORT_FIELD_PATH, [], 'ezrepoforms_content_type') => Location::SORT_FIELD_PATH,
                    $this->translator->trans(/** @Ignore */'content_type.sort_field.' . Location::SORT_FIELD_PRIORITY, [], 'ezrepoforms_content_type') => Location::SORT_FIELD_PRIORITY,
                    $this->translator->trans(/** @Ignore */'content_type.sort_field.' . Location::SORT_FIELD_MODIFIED, [], 'ezrepoforms_content_type') => Location::SORT_FIELD_MODIFIED,
                    $this->translator->trans(/** @Ignore */'content_type.sort_field.' . Location::SORT_FIELD_PUBLISHED, [], 'ezrepoforms_content_type') => Location::SORT_FIELD_PUBLISHED,
                    $this->translator->trans(/** @Ignore */'content_type.sort_field.' . Location::SORT_FIELD_SECTION, [], 'ezrepoforms_content_type') => Location::SORT_FIELD_SECTION,
                ],
                'choices_as_values' => true,
                'label' => 'content_type.default_sort_field',
            ])
            ->add('defaultSortOrder', ChoiceType::class, [
                'choices' => [
                    $this->translator->trans(/** @Ignore */'content_type.sort_order.' . Location::SORT_ORDER_ASC, [], 'ezrepoforms_content_type') => Location::SORT_ORDER_ASC,
                    $this->translator->trans(/** @Ignore */'content_type.sort_order.' . Location::SORT_ORDER_DESC, [], 'ezrepoforms_content_type') => Location::SORT_ORDER_DESC,
                ],
                'choices_as_values' => true,
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
            ->add('fieldTypeSelection', ChoiceType::class, [
                'choices' => array_flip($this->getFieldTypeList()),
                'choices_as_values' => true,
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

    /**
     * Returns a hash, with fieldType identifiers as keys and human readable names as values.
     *
     * @return array
     */
    private function getFieldTypeList()
    {
        $list = [];
        foreach ($this->fieldTypeCollectionFactory->getConcreteFieldTypesIdentifiers() as $fieldTypeIdentifier) {
            $list[$fieldTypeIdentifier] = $this->translator->trans(/** @Ignore */"$fieldTypeIdentifier.name", [], 'fieldtypes');
        }

        asort($list, SORT_NATURAL);

        return $list;
    }
}
