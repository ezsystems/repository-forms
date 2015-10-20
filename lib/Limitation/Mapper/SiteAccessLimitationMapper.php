<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Limitation\Mapper;

class SiteAccessLimitationMapper extends MultipleSelectionBasedMapper
{
    /**
     * @var array
     */
    private $siteAccessList;

    public function __construct(array $siteAccessList)
    {
        $this->siteAccessList = $siteAccessList;
    }

    protected function getSelectionChoices()
    {
        $siteAccesses = [];
        foreach ($this->siteAccessList as $sa) {
            $siteAccesses[sprintf('%u', crc32($sa))] = $sa;
        }

        return $siteAccesses;
    }

    protected function getChoiceFieldOptions()
    {
        return ['required' => false];
    }
}
