<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Limitation\Mapper;

use eZ\Publish\API\Repository\SectionService;

class SectionLimitationMapper extends MultipleSelectionBasedMapper
{
    /**
     * @var SectionService
     */
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    protected function getSelectionChoices()
    {
        $choices = [];
        foreach ($this->sectionService->loadSections() as $section) {
            $choices[$section->id] = $section->name;
        }

        return $choices;
    }
}
