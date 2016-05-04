<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\FormInterface;

class UserFormMapper implements FieldValueFormMapperInterface
{
    /**
     * "Maps" Field form to current FieldType.
     * Allows to add form fields for content edition.
     *
     * @param FormInterface $fieldForm Form for the current Field.
     * @param FieldData $data Underlying data for current Field form.
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $label = $fieldDefinition->getName($formConfig->getOption('languageCode')) ?: reset($fieldDefinition->getNames());

        $fieldForm->add(
            'value',
            'ezuser',
            ['required' => $fieldDefinition->isRequired, 'label' => $label]
        );
    }
}
