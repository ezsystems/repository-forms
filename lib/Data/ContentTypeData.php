<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Data;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeUpdateStruct;

/**
 * Base data class for ContentType update form, with FieldDefinitions data and ContentTypeDraft.
 *
 * @author Jérôme Vieilledent <jerome.vieilledent@ez.no>
 *
 * @property-read \EzSystems\RepositoryForms\Data\FieldDefinitionData[] $fieldDefinitionsData
 */
class ContentTypeData extends ContentTypeUpdateStruct
{
    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft
     */
    protected $contentTypeDraft;

    /**
     * @var \EzSystems\RepositoryForms\Data\FieldDefinitionData[]
     */
    protected $fieldDefinitionsData = [];

    public function addFieldDefinitionData(FieldDefinitionData $fieldDefinitionData)
    {
        $this->fieldDefinitionsData[] = $fieldDefinitionData;
    }
}
