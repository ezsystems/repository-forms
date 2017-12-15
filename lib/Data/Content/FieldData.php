<?php
/**
 * This file is part of the eZ Repository Forms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Content;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * @property-read \eZ\Publish\API\Repository\Values\Content\Field $field
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
 */
class FieldData extends ValueObject
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected $field;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    protected $fieldDefinition;

    /**
     * @var mixed
     */
    public $value;

    public function getFieldTypeIdentifier()
    {
        return $this->fieldDefinition->fieldTypeIdentifier;
    }
}
