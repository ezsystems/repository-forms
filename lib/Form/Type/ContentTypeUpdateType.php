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
use EzSystems\RepositoryForms\Form\DataTransformer\TranslatablePropertyTransformer;
use EzSystems\RepositoryForms\Form\Type\FieldDefinition\FieldDefinitionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for ContentType update.
 *
 * @author Jérôme Vieilledent <jerome.vieilledent@ez.no>
 */
class ContentTypeUpdateType extends AbstractType
{
    public function getName()
    {
        return 'ezrepoforms_contenttype_update';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'EzSystems\RepositoryForms\Data\ContentTypeData'
            ])
            ->setRequired(['languageCode']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translatablePropertyTransformer = new TranslatablePropertyTransformer($options['languageCode']);
        $builder
            ->add(
                $builder
                    ->create('name', 'text', ['property_path' => 'names'])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('identifier', 'text')
            ->add(
                $builder
                    ->create('description', 'text', ['property_path' => 'descriptions', 'required' => false])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('nameSchema', 'text', ['required' => false])
            ->add('urlAliasSchema', 'text', ['required' => false])
            ->add('isContainer', 'checkbox', ['required' => false])
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
                ]
            ])
            ->add('defaultSortOrder', 'choice', [
                'choices' => [
                    Location::SORT_ORDER_ASC => 'Ascending',
                    Location::SORT_ORDER_DESC => 'Descending',
                ]
            ])
            ->add('defaultAlwaysAvailable', 'checkbox', ['required' => false])
            ->add('fieldDefinitionsData', 'collection', [
                'type' => new FieldDefinitionType(),
                'options' => ['languageCode' => $options['languageCode']]
            ])
            ->add('fieldTypeSelection', 'choice', [
                'choices' => ['ezstring' => 'Text line'],
                'mapped' => false
            ])
            ->add('addFieldDefinition', 'submit', ['label' => 'Add field definition'])
            ->add('saveContentType', 'submit', ['label' => 'Update']);
    }
}
