<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Type\Content;

use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentFieldType extends AbstractType
{
    /**
     * @var FieldTypeFormMapperRegistryInterface
     */
    private $fieldTypeFormMapperRegistry;

    public function __construct(FieldTypeFormMapperRegistryInterface $fieldTypeFormMapperRegistry)
    {
        $this->fieldTypeFormMapperRegistry = $fieldTypeFormMapperRegistry;
    }

    public function getName()
    {
        return 'ezrepoforms_content_field';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => '\EzSystems\RepositoryForms\Data\Content\FieldData',
                'translation_domain' => 'ezrepoforms_content',
            ])
            ->setRequired(['languageCode']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \EzSystems\RepositoryForms\Data\Content\FieldData $data */
            $data = $event->getData();
            $form = $event->getForm();

            $fieldTypeIdentifier = $data->fieldDefinition->fieldTypeIdentifier;
            if ($this->fieldTypeFormMapperRegistry->hasMapper($fieldTypeIdentifier)) {
                $this->fieldTypeFormMapperRegistry->getMapper($fieldTypeIdentifier)->mapFieldForm($form, $data);
            }
        });
    }
}
