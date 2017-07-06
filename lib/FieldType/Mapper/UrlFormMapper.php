<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\RepositoryForms\Form\Type\FieldValue\UrlType;
use Symfony\Component\Form\FormInterface;

class UrlFormMapper implements FieldValueFormMapperInterface
{
    /**
     * Maps Field form to current FieldType.
     * Allows to add form fields for content edition.
     *
     * @param FormInterface $form Form for the current Field.
     * @param FieldData $data Underlying data for current Field form.
     */
    public function mapFieldValueForm(FormInterface $form, FieldData $data)
    {
        $form->add(
            'value',
            UrlType::class,
            ['required' => $data->fieldDefinition->isRequired]
        );
    }
}
