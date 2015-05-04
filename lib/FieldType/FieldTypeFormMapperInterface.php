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
     * Returns a hash containing config for edit form.
     * Can return an empty array if no config is required. In that case, fields defined in mapFieldDefinition()
     * will be rendered with default label/errors/widget views.
     *
     * The hash may contain following keys:
     * - "template": Template where specific form fields defined in mapFieldDefinitionForm() will be displayed.
     *   Default passed variables are:
     *     - "data": FieldDefinitionData object
     *     - "languageCode": Language code as passed to the main form
     *     - "contentTypeDraft": The ContentTypeDraft object
     * - "vars": Hash of additional variables to pass to the template.
     *
     * @return array
     */
    public function getFieldDefinitionEditConfig();
}
