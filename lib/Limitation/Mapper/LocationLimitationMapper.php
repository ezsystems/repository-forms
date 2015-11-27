<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation;

class LocationLimitationMapper extends UDWBasedMapper
{
    public function filterLimitationValues(Limitation $limitation)
    {
        $limitation->limitationValues = [$limitation->limitationValues];
    }
}
