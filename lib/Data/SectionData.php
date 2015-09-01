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
     * Trait which provides isNew(), and mandates getIdentifierValue().
     */
    use NewnessChecker;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Section
     */
    protected $section;

    public function getId()
    {
        return $this->section->id;
    }

    /**
     * Returns the value of the property which can be considered as the value object identifier.
     *
     * @return string
     */
    protected function getIdentifierValue()
    {
        return $this->section->identifier;
    }
}
