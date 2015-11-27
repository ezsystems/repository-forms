<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Limitation\Mapper;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\User\Limitation;

class SubtreeLimitationMapper extends UDWBasedMapper
{
    /**
     * @var LocationService
     */
    private $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function filterLimitationValues(Limitation $limitation)
    {
        if (!is_array($limitation->limitationValues)) {
            return;
        }

        // UDW returns an array of location IDs. If we haven't used UDW, the value is as stored: an array of path strings.
        foreach ($limitation->limitationValues as $key => $limitationValue) {
            if (preg_match('/\A\d+\z/', $limitationValue) === 1) {
                $limitation->limitationValues[$key] = $this->locationService->loadLocation($limitationValue)->pathString;
            }
        }
    }
}
