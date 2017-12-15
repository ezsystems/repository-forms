<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Section;

use eZ\Publish\API\Repository\Values\Content\SectionCreateStruct;
use EzSystems\RepositoryForms\Data\NewnessCheckable;

/**
 * @property-read \eZ\Publish\API\Repository\Values\Content\Section $section
 */
class SectionCreateData extends SectionCreateStruct implements NewnessCheckable
{
    use SectionDataTrait;

    public function isNew()
    {
        return true;
    }
}
