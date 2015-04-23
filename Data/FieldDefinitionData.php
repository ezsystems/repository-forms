<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryFormsBundle\Data;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionUpdateStruct;

/**
 * Base class for FieldDefinition forms, with corresponding FieldDefinition object.
 *
 * @author Jérôme Vieilledent <jerome.vieilledent@ez.no>
 *
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
 */
class FieldDefinitionData extends FieldDefinitionUpdateStruct
{
    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    protected $fieldDefinition;

    public function getFieldTypeIdentifier()
    {
        return $this->fieldDefinition->fieldTypeIdentifier;
    }
}
