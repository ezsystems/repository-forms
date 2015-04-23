<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Form\Type\FieldDefinition;

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
            // TODO: To be refactored cleanly with a registry and an interface.
            // FieldTypes may define a service that would alter the form for their own needs.
            switch ($data->getFieldTypeIdentifier()) {
                case 'ezstring':
                    $form
                        ->add('minLength', 'integer', [
                            'required' => false,
                            'property_path' => 'validatorConfiguration[StringLengthValidator][minStringLength]',
                        ])
                        ->add('maxLength', 'integer', [
                            'required' => false,
                            'property_path' => 'validatorConfiguration[StringLengthValidator][maxStringLength]',
                        ]);
                    break;
            }
        });
    }

    public function getName()
    {
        return 'ezrepoforms_fielddefinition_update';
    }
}
