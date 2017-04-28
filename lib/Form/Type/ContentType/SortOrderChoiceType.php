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
 * Form type for sort order selection.
 */
class SortOrderChoiceType extends AbstractType
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
        return 'ezrepoforms_contenttype_sort_order_choice';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getSortOrderChoices(),
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
     * Generate sort order options available to choose.
     *
     * @return array
     */
    private function getSortOrderChoices()
    {
        $choices = [];
        foreach ($this->getSortOrderValues() as $value) {
            $choices[$this->getSortOrderLabel($value)] = $value;
        }

        return $choices;
    }

    /**
     * Generate human readable label for sort order.
     *
     * @param string $sortOrder
     * @return string
     */
    private function getSortOrderLabel($sortOrder)
    {
        return $this->translator->trans(/** @Ignore */'content_type.sort_order.' . $sortOrder, [], 'ezrepoforms_content_type');
    }

    /**
     * Get available sort order values.
     *
     * @return array
     */
    private function getSortOrderValues()
    {
        return [
            Location::SORT_ORDER_ASC,
            Location::SORT_ORDER_DESC,
        ];
    }
}
