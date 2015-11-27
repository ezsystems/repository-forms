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
        // UDW returns a location ID. If we haven't used UDW, the value is as stored: a path string.
        if (preg_match('/\A\d+\z/', $limitation->limitationValues) === 1) {
            $limitation->limitationValues = [$this->locationService->loadLocation($limitation->limitationValues)->pathString];
        } else {
            $limitation->limitationValues = [$limitation->limitationValues];
        }
    }
}
