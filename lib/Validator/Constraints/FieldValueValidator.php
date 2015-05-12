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
use Symfony\Component\Validator\Constraint;

/**
 * Base class for field value validators.
 */
abstract class FieldValueValidator extends FieldTypeValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ValueObject) {
            return;
        }

        $fieldValue = $this->getFieldValue($value);
        if (!$fieldValue) {
            return;
        }

        $fieldTypeIdentifier = $this->getFieldTypeIdentifier($value);
        $fieldDefinition = $this->getFieldDefinition($value);
        $fieldType = $this->fieldTypeService->getFieldType($fieldTypeIdentifier);
        $this->processValidationErrors($fieldType->validateValue($fieldDefinition, $fieldValue));
    }

    /**
     * Returns the field value to validate, or null if there is nothing to validate (e.g. empty default value)
     *
     * @param ValueObject $value ValueObject holding the field value to validate, e.g. FieldDefinitionData.
     *
     * @throws \InvalidArgumentException If field value cannot be retrieved.
     *
     * @return \eZ\Publish\SPI\FieldType\Value|null
     */
    abstract protected function getFieldValue(ValueObject $value);

    /**
     * Returns the field definition $value refers to.
     * FieldDefinition object is needed to validate field value against field settings.
     *
     * @param ValueObject $value ValueObject holding the field value to validate, e.g. FieldDefinitionData.
     *
     * @throws \InvalidArgumentException If field definition cannot be retrieved.
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    abstract protected function getFieldDefinition(ValueObject $value);

    /**
     * Returns the fieldTypeIdentifier for the field value to validate.
     *
     * @param ValueObject $value ValueObject holding the field value to validate, e.g. FieldDefinitionData.
     *
     * @return string
     */
    abstract protected function getFieldTypeIdentifier(ValueObject $value);
}
