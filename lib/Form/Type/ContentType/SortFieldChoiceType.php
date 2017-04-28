<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\ContentType;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Form type for sort field selection.
 */
class SortFieldChoiceType extends AbstractType
{
    /**
     * @var Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_contenttype_sort_field_choice';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getSortFieldChoices(),
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
     * Generate sort field options available to choose.
     *
     * @return array
     */
    private function getSortFieldChoices()
    {
        $choices = [];
        foreach ($this->getSortFieldValues() as $sortField) {
            $choices[$this->getSortFieldLabel($sortField)] = $sortField;
        }

        return $choices;
    }

    /**
     * Generate human readable label for sort field.
     *
     * @param string $sortField
     * @return string
     */
    private function getSortFieldLabel($sortField)
    {
        return $this->translator->trans(/** @Ignore */'content_type.sort_field.' . $sortField, [], 'ezrepoforms_content_type');
    }

    /**
     * Returns available sort field values.
     *
     * @return array
     */
    private function getSortFieldValues()
    {
        return [
            Location::SORT_FIELD_NAME,
            Location::SORT_FIELD_CLASS_NAME,
            Location::SORT_FIELD_CLASS_IDENTIFIER,
            Location::SORT_FIELD_DEPTH,
            Location::SORT_FIELD_PATH,
            Location::SORT_FIELD_PRIORITY,
            Location::SORT_FIELD_MODIFIED,
            Location::SORT_FIELD_PUBLISHED,
            Location::SORT_FIELD_SECTION,
        ];
    }
}
