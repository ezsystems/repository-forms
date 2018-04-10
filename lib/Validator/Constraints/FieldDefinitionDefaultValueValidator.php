<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Validator\Constraints;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;

/**
 * Validator for default value from FieldDefinitionData.
 */
class FieldDefinitionDefaultValueValidator extends FieldValueValidator
{
    protected function canValidate($value)
    {
        return $value instanceof FieldDefinitionData;
    }

    /**
     * Returns the field value to validate.
     *
     * @param FieldDefinitionData|ValueObject $value ValueObject holding the field value to validate, e.g. FieldDefinitionData.
     *
     * @throws \InvalidArgumentException If field value cannot be retrieved.
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    protected function getFieldValue(ValueObject $value)
    {
        return $value->defaultValue;
    }

    /**
     * Returns the fieldTypeIdentifier for the field value to validate.
     *
     * @param FieldDefinitionData|ValueObject $value ValueObject holding the field value to validate, e.g. FieldDefinitionData.
     *
     * @return string
     */
    protected function getFieldTypeIdentifier(ValueObject $value)
    {
        return $value->getFieldTypeIdentifier();
    }

    protected function generatePropertyPath($errorIndex, $errorTarget)
    {
        return 'defaultValue';
    }

    /**
     * Returns the field definition $value refers to.
     * FieldDefinition object is needed to validate field value against field settings.
     *
     * @param FieldData|ValueObject $value ValueObject holding the field value to validate, e.g. FieldDefinitionData.
     *
     * @throws \InvalidArgumentException If field definition cannot be retrieved.
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    protected function getFieldDefinition(ValueObject $value)
    {
        return $this->getUpdatedFieldDefinition($value);
    }

    /**
     * This method overwrite a property fieldSettings in the FieldDefinition object to expose sent settings.
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $value
     *
     * @return \eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition
     */
    private function getUpdatedFieldDefinition(ValueObject $value): FieldDefinition
    {
        $oldFieldDef = $value->fieldDefinition;

        $properties = [];
        foreach ($oldFieldDef->attributes() as $property) {
            $properties[$property] = $oldFieldDef->{$property};
        }
        $properties['fieldSettings'] = $value->fieldSettings;

        return new FieldDefinition($properties);
    }
}
