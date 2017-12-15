<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Section;

use eZ\Publish\API\Repository\Values\Content\Section;

trait SectionDataTrait
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Section
     */
    protected $section;

    public function setSection(Section $section)
    {
        $this->section = $section;
    }

    public function getId()
    {
        return $this->section ? $this->section->id : null;
    }
}
