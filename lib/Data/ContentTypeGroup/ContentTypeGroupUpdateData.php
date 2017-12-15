<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\ContentTypeGroup;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroupUpdateStruct;
use EzSystems\RepositoryForms\Data\NewnessChecker;

/**
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
 */
class ContentTypeGroupUpdateData extends ContentTypeGroupUpdateStruct
{
    use ContentTypeGroupDataTrait, NewnessChecker;

    protected function getIdentifierValue()
    {
        return $this->contentTypeGroup->identifier;
    }
}
