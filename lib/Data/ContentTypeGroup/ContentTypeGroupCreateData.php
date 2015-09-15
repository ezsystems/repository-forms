<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\ContentTypeGroup;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroupCreateStruct;
use EzSystems\RepositoryForms\Data\NewsnessCheckable;

/**
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
 */
class ContentTypeGroupCreateData extends ContentTypeGroupCreateStruct implements NewsnessCheckable
{
    use ContentTypeGroupDataTrait;

    public function isNew()
    {
        return true;
    }
}
