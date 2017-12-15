<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Content;

use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use EzSystems\RepositoryForms\Data\NewnessCheckable;

/**
 * @property-read \EzSystems\RepositoryForms\Data\Content\FieldData[] $fieldsData
 */
class ContentCreateData extends ContentCreateStruct implements NewnessCheckable
{
    use ContentData;

    /**
     * @var LocationCreateStruct[]
     */
    private $locationStructs;

    public function isNew()
    {
        return true;
    }

    /**
     * @return LocationCreateStruct[]
     */
    public function getLocationStructs()
    {
        return $this->locationStructs;
    }

    /**
     * Adds a location struct.
     * A location will be created out of it, bound to the created content.
     *
     * @param LocationCreateStruct $locationStruct
     */
    public function addLocationStruct(LocationCreateStruct $locationStruct)
    {
        $this->locationStructs[] = $locationStruct;
    }
}
