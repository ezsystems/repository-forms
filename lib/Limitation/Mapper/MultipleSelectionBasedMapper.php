<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\RepositoryForms\Limitation\LimitationFormMapperInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Base class for mappers based on multiple selection.
 */
abstract class MultipleSelectionBasedMapper implements LimitationFormMapperInterface
{
    /**
     * Form template to use.
     *
     * @var string
     */
    private $template;

    public function mapLimitationForm(FormInterface $form, Limitation $data)
    {
        $options = $this->getChoiceFieldOptions() + [
            'multiple' => true,
            'label' => $data->getIdentifier(),
            'required' => false,
        ];
        $choices = $this->getSelectionChoices();
        asort($choices, SORT_NATURAL | SORT_FLAG_CASE);
        $options += ['choices' => $choices];
        $form->add('limitationValues', 'choice', $options);
    }

    /**
     * Returns value choices to display, as expected by the "choices" option from Choice field.
     *
     * @return array
     */
    abstract protected function getSelectionChoices();

    /**
     * Returns custom options.
     *
     * @return array
     */
    protected function getChoiceFieldOptions()
    {
        return [];
    }

    public function setFormTemplate($template)
    {
        $this->template = $template;
    }

    public function getFormTemplate()
    {
        return $this->template;
    }

    public function filterLimitationValues(Limitation $limitation)
    {
    }
}
