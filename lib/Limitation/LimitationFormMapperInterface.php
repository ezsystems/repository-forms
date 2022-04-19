<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Limitation;

use eZ\Publish\API\Repository\Values\User\Limitation;
use Symfony\Component\Form\FormInterface;

/**
 * Interface for LimitationType form mappers.
 *
 * It maps a LimitationType's supported values to editing form.
 */
interface LimitationFormMapperInterface
{
    /**
     * "Maps" Limitation form to current LimitationType, in order to display one or several fields
     * representing limitation values supported by the LimitationType.
     *
     * Implementors MUST either:
     * - Add a "limitationValues" form field
     * - OR add field(s) that map to "limitationValues" property from $data.
     *
     * @param FormInterface $form Form for current Limitation.
     * @param Limitation $data Underlying data for current Limitation form.
     */
    public function mapLimitationForm(FormInterface $form, Limitation $data);

    /**
     * Returns the Twig template to use to render the limitation form.
     *
     * @return string
     */
    public function getFormTemplate();

    /**
     * This method will be called when FormEvents::SUBMIT is called.
     * It gives the opportunity to filter/manipulate limitation values.
     */
    public function filterLimitationValues(Limitation $limitation);
}
