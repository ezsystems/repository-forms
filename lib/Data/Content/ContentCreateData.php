<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\Content;

use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use EzSystems\RepositoryForms\Data\NewnessCheckable;

/**
 * @property-read \EzSystems\RepositoryForms\Data\Content\FieldData[] $fieldsData
 */
class ContentCreateData extends ContentCreateStruct implements NewnessCheckable
{
    use ContentData;

    public function isNew()
    {
        return true;
    }
}
