<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data;

use eZ\Publish\API\Repository\Values\Content\SectionUpdateStruct;

/**
 * @property-read \eZ\Publish\API\Repository\Values\Content\Section $section
 */
class SectionData extends SectionUpdateStruct
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Section
     */
    protected $section;

    public function getId()
    {
        return $this->section->id;
    }

    public function isNew()
    {
        return strpos($this->section->identifier, '__new__') === 0;
    }
}
