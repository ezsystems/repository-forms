<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\ContentType;

use eZ\Publish\Core\Base\Container\ApiLoader\FieldTypeCollectionFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Form type for field type selection.
 */
class FieldTypeChoiceType extends AbstractType
{
    /**
     * @var eZ\Publish\Core\Base\Container\ApiLoader\FieldTypeCollectionFactory
     */
    private $fieldTypeCollectionFactory;

    /**
     * @var Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    public function __construct(FieldTypeCollectionFactory $fieldTypeCollectionFactory, TranslatorInterface $translator)
    {
        $this->fieldTypeCollectionFactory = $fieldTypeCollectionFactory;
        $this->translator = $translator;
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_contenttype_field_type_choice';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getFieldTypeChoices(),
            'choices_as_values' => true,
        ]);
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * Returns a hash, with fieldType identifiers as keys and human readable names as values.
     *
     * @return array
     */
    private function getFieldTypeChoices()
    {
        $choices = [];
        foreach ($this->fieldTypeCollectionFactory->getConcreteFieldTypesIdentifiers() as $fieldTypeIdentifier) {
            $choices[$this->getFieldTypeLabel($fieldTypeIdentifier)] = $fieldTypeIdentifier;
        }

        ksort($choices, SORT_NATURAL);

        return $choices;
    }

    /**
     * Generate a human readable name for field type identifier.
     *
     * @param string $fieldTypeIdentifier
     * @return string
     */
    private function getFieldTypeLabel($fieldTypeIdentifier)
    {
        return $this->translator->trans(/** @Ignore */$fieldTypeIdentifier . '.name', [], 'fieldtypes');
    }
}
