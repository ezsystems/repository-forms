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
use Symfony\Component\Form\AbstractType;
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
        $translatablePropertyTransformer = new TranslatablePropertyTransformer($options['languageCode']);
        $builder
            ->add(
                $builder
                    ->create('name', 'text', ['property_path' => 'names', 'label' => 'content_type.name'])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('identifier', 'text', ['label' => 'content_type.identifier'])
            ->add(
                $builder
                    ->create('description', 'text', [
                        'property_path' => 'descriptions',
                        'required' => false,
                        'label' => 'content_type.description',
                    ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('nameSchema', 'text', ['required' => false, 'label' => 'content_type.name_schema'])
            ->add('urlAliasSchema', 'text', ['required' => false, 'label' => 'content_type.url_alias_schema'])
            ->add('isContainer', 'checkbox', ['required' => false, 'label' => 'content_type.is_container'])
            ->add('defaultSortField', 'choice', [
                'choices' => [
                    Location::SORT_FIELD_NAME => $this->translator->trans('content_type.sort_field.' . Location::SORT_FIELD_NAME, [], 'ezrepoforms_content_type'),
                    Location::SORT_FIELD_CLASS_NAME => $this->translator->trans('content_type.sort_field.' . Location::SORT_FIELD_CLASS_NAME, [], 'ezrepoforms_content_type'),
                    Location::SORT_FIELD_CLASS_IDENTIFIER => $this->translator->trans('content_type.sort_field.' . Location::SORT_FIELD_CLASS_IDENTIFIER, [], 'ezrepoforms_content_type'),
                    Location::SORT_FIELD_DEPTH => $this->translator->trans('content_type.sort_field.' . Location::SORT_FIELD_DEPTH, [], 'ezrepoforms_content_type'),
                    Location::SORT_FIELD_PATH => $this->translator->trans('content_type.sort_field.' . Location::SORT_FIELD_PATH, [], 'ezrepoforms_content_type'),
                    Location::SORT_FIELD_PRIORITY => $this->translator->trans('content_type.sort_field.' . Location::SORT_FIELD_PRIORITY, [], 'ezrepoforms_content_type'),
                    Location::SORT_FIELD_MODIFIED => $this->translator->trans('content_type.sort_field.' . Location::SORT_FIELD_MODIFIED, [], 'ezrepoforms_content_type'),
                    Location::SORT_FIELD_PUBLISHED => $this->translator->trans('content_type.sort_field.' . Location::SORT_FIELD_PUBLISHED, [], 'ezrepoforms_content_type'),
                    Location::SORT_FIELD_SECTION => $this->translator->trans('content_type.sort_field.' . Location::SORT_FIELD_SECTION, [], 'ezrepoforms_content_type'),
                ],
                'label' => 'content_type.default_sort_field',
            ])
            ->add('defaultSortOrder', 'choice', [
                'choices' => [
                    Location::SORT_ORDER_ASC => $this->translator->trans('content_type.sort_order.' . Location::SORT_ORDER_ASC, [], 'ezrepoforms_content_type'),
                    Location::SORT_ORDER_DESC => $this->translator->trans('content_type.sort_order.' . Location::SORT_ORDER_DESC, [], 'ezrepoforms_content_type'),
                ],
                'label' => 'content_type.default_sort_order',
            ])
            ->add('defaultAlwaysAvailable', 'checkbox', [
                'required' => false,
                'label' => 'content_type.default_always_available',
            ])
            ->add('fieldDefinitionsData', 'collection', [
                'type' => 'ezrepoforms_fielddefinition_update',
                'options' => ['languageCode' => $options['languageCode']],
                'label' => 'content_type.field_definitions_data',
            ])
            ->add('fieldTypeSelection', 'choice', [
                'choices' => $this->getFieldTypeList(),
                'mapped' => false,
                'label' => 'content_type.field_type_selection',
            ])
            ->add('addFieldDefinition', 'submit', ['label' => 'content_type.add_field_definition'])
            ->add('removeFieldDefinition', 'submit', ['label' => 'content_type.remove_field_definitions'])
            ->add('saveContentType', 'submit', ['label' => 'content_type.save'])
            ->add('removeDraft', 'submit', ['label' => 'content_type.remove_draft', 'validation_groups' => false])
            ->add('publishContentType', 'submit', ['label' => 'content_type.publish']);
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
            $list[$fieldTypeIdentifier] = $this->translator->trans("$fieldTypeIdentifier.name", [], 'fieldtypes');
        }

        asort($list, SORT_NATURAL);

        return $list;
    }
}
