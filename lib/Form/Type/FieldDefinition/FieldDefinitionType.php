<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Form\Type\FieldDefinition;

use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface;
use EzSystems\RepositoryForms\Form\DataTransformer\TranslatablePropertyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for FieldDefinition update.
 *
 * @author Jérôme Vieilledent <jerome.vieilledent@ez.no>
 */
class FieldDefinitionType extends AbstractType
{
    /**
     * @var FieldTypeFormMapperInterface
     */
    private $fieldTypeMapperRegistry;

    public function __construct(FieldTypeFormMapperInterface $fieldTypeMapperRegistry)
    {
        $this->fieldTypeMapperRegistry = $fieldTypeMapperRegistry;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'EzSystems\RepositoryForms\Data\FieldDefinitionData'
            ])
            ->setRequired(['languageCode']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translatablePropertyTransformer = new TranslatablePropertyTransformer($options['languageCode']);
        $builder
            ->add('fieldTypeIdentifier', 'hidden')
            ->add(
                $builder->create('name', 'text', ['property_path' => 'names'])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('identifier', 'text', ['required' => true])
            ->add(
                $builder->create('description', 'text', ['property_path' => 'descriptions', 'required' => false])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('isRequired', 'checkbox', ['required' => false])
            ->add('isSearchable', 'checkbox', ['required' => false])
            ->add('isTranslatable', 'checkbox', ['required' => false])
            ->add('fieldGroup', 'choice', ['choices' => []], ['required' => false])
            ->add('position', 'integer');

        // Hook on form generation for specific FieldType needs
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var \EzSystems\RepositoryForms\Data\FieldDefinitionData $data */
            $data = $event->getData();
            $form = $event->getForm();
            // Let fieldType mappers do their jobs to complete the form.
            $this->fieldTypeMapperRegistry->mapFieldDefinitionForm($form, $data);
        });
    }

    public function getName()
    {
        return 'ezrepoforms_fielddefinition_update';
    }
}
