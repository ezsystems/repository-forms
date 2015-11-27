<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Validator\Constraints;


use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use Symfony\Component\Validator\Constraint;

/**
 * Validator for content value.
 */
class ContentFieldValidator extends FieldValueValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof FieldData) {
            return;
        }

        parent::validate($value, $constraint);
    }

    /**
     * Returns the field value to validate, or null if there is nothing to validate (e.g. empty default value).
     *
     * @param FieldData|ValueObject $value ValueObject holding the field value to validate, e.g. FieldData.
     *
     * @throws \InvalidArgumentException If field value cannot be retrieved.
     *
     * @return \eZ\Publish\SPI\FieldType\Value|null
     */
    protected function getFieldValue(ValueObject $value)
    {
        return $value->value;
    }

    /**
     * Returns the field definition $value refers to.
     * FieldDefinition object is needed to validate field value against field settings.
     *
     * @param FieldData|ValueObject $value ValueObject holding the field value to validate, e.g. FieldData.
     *
     * @throws \InvalidArgumentException If field definition cannot be retrieved.
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    protected function getFieldDefinition(ValueObject $value)
    {
        return $value->fieldDefinition;
    }

    /**
     * Returns the fieldTypeIdentifier for the field value to validate.
     *
     * @param FieldData|ValueObject $value ValueObject holding the field value to validate, e.g. FieldData.
     *
     * @return string
     */
    protected function getFieldTypeIdentifier(ValueObject $value)
    {
        return $value->fieldDefinition->fieldTypeIdentifier;
    }

    protected function generatePropertyPath($errorIndex, $errorTarget)
    {
        return 'value';
    }
}
