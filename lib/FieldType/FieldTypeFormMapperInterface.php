<?php
/**
 * This file is part of the eZ RepositoryFroms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\FieldType;

use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use Symfony\Component\Form\FormInterface;

/**
 * Interface for FieldType form mappers.
 *
 * It maps a FieldType's specifics to editing Forms (e.g. FieldDefinition forms).
 */
interface FieldTypeFormMapperInterface
{
    /**
     * "Maps" FieldDefinition form to current FieldType.
     * Gives the opportunity to enrich $fieldDefinitionForm with custom fields for:
     * - validator configuration,
     * - field settings
     * - default value
     *
     * @param FormInterface $fieldDefinitionForm Form for current FieldDefinition.
     * @param FieldDefinitionData $data Underlying data for current FieldDefinition form.
     *
     * @return void
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data);

    /**
     * Returns human readable name of the FieldType (e.g. "Text line")
     *
     * @return string
     */
    public function getName();
}