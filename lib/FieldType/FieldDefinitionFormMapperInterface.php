<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType;

use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use Symfony\Component\Form\FormInterface;

/**
 * A field definition mapper maps FieldDefinition to a FieldDefinitionForm.
 *
 * Each FieldType will implement its own, depending on the form elements it takes to configure a FieldDefinition.
 */
interface FieldDefinitionFormMapperInterface extends FieldFormMapperInterface
{
    /**
     * "Maps" FieldDefinition form to current FieldType.
     * Gives the opportunity to enrich $fieldDefinitionForm with custom fields for:
     * - validator configuration,
     * - field settings
     * - default value.
     *
     * @param FormInterface $fieldDefinitionForm Form for current FieldDefinition.
     * @param FieldDefinitionData $data Underlying data for current FieldDefinition form.
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data);
}
