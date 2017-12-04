<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\Content;

use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use EzSystems\RepositoryForms\Data\NewnessCheckable;

/**
 * @property-read \EzSystems\RepositoryForms\Data\Content\FieldData[] $fieldsData
 * @property-read \eZ\Publish\API\Repository\Values\Content\Content $contentDraft
 */
class ContentUpdateData extends ContentUpdateStruct implements NewnessCheckable
{
    use ContentData;

    protected $contentDraft;

    public function isNew()
    {
        return false;
    }
}
