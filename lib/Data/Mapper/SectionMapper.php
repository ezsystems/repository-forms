<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Data\SectionUpdateData;

class SectionMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param ValueObject|\eZ\Publish\API\Repository\Values\Content\Section $section
     * @param array $params
     *
     * @return mixed
     */
    public function mapToFormData(ValueObject $section, array $params = [])
    {
        $data = new SectionUpdateData(['section' => $section]);
        if (!$data->isNew()) {
            $data->identifier = $section->identifier;
            $data->name = $section->name;
        }

        return $data;
    }
}
