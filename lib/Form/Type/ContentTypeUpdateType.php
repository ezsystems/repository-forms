<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Form\Type;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperRegistryInterface;
use EzSystems\RepositoryForms\Form\DataTransformer\TranslatablePropertyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Form type for ContentType update.
 */
class ContentTypeUpdateType extends AbstractType
{
    /**
     * @var FieldTypeFormMapperRegistryInterface
     */
    private $fieldTypeFormMapperRegistry;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(FieldTypeFormMapperRegistryInterface $fieldTypeFormMapperRegistry, TranslatorInterface $translator)
    {
        $this->fieldTypeFormMapperRegistry = $fieldTypeFormMapperRegistry;
        $this->translator = $translator;
    }

    public function getName()
    {
        return 'ezrepoforms_contenttype_update';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
                        'label' => 'content_type.description'
                    ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('nameSchema', 'text', ['required' => false, 'label' => 'content_type.name_schema'])
            ->add('urlAliasSchema', 'text', ['required' => false, 'label' => 'content_type.url_alias_schema'])
            ->add('isContainer', 'checkbox', ['required' => false, 'label' => 'content_type.is_container'])
            ->add('defaultSortField', 'choice', [
                'choices' => [
                    Location::SORT_FIELD_NAME => 'Content name',
                    Location::SORT_FIELD_CLASS_NAME => 'ContentType name',
                    Location::SORT_FIELD_CLASS_IDENTIFIER => 'ContentType identifier',
                    Location::SORT_FIELD_DEPTH => 'Location depth',
                    Location::SORT_FIELD_PATH => 'Location path',
                    Location::SORT_FIELD_PRIORITY => 'Location priority',
                    Location::SORT_FIELD_MODIFIED => 'Modification date',
                    Location::SORT_FIELD_PUBLISHED => 'Publication date',
                    Location::SORT_FIELD_SECTION => 'Section',
                ],
                'label' => 'content_type.default_sort_field',
            ])
            ->add('defaultSortOrder', 'choice', [
                'choices' => [
                    Location::SORT_ORDER_ASC => 'Ascending',
                    Location::SORT_ORDER_DESC => 'Descending',
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
            ->add('saveContentType', 'submit', ['label' => 'content_type.save']);
    }

    /**
     * Returns a hash, with fieldType identifiers as keys and human readable names as values.
     *
     * @return array
     */
    private function getFieldTypeList()
    {
        $list = [];
        foreach ($this->fieldTypeFormMapperRegistry->getMappers() as $fieldTypeIdentifier => $mapper) {
            $list[$fieldTypeIdentifier] = $this->translator->trans("$fieldTypeIdentifier.name", [], 'fieldtypes');
        }

        return $list;
    }
}
