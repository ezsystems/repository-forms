<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Data;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionUpdateStruct;

/**
 * Base class for FieldDefinition forms, with corresponding FieldDefinition object.
 *
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
 * @property-read \EzSystems\RepositoryForms\Data\ContentTypeData $contentTypeData
 */
class FieldDefinitionData extends FieldDefinitionUpdateStruct
{
    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    protected $fieldDefinition;

    /**
     * ContentTypeData holding current FieldDefinitionData.
     * Mainly used for validation.
     *
     * @var \EzSystems\RepositoryForms\Data\ContentTypeData
     */
    protected $contentTypeData;

    public function getFieldTypeIdentifier()
    {
        return $this->fieldDefinition->fieldTypeIdentifier;
    }
}
