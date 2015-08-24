<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Data\RoleData;

class RoleMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data
     * (e.g. create/update struct).
     *
     * @param ValueObject|\eZ\Publish\API\Repository\Values\User\Role $role
     * @param array $params
     *
     * @return RoleData
     */
    public function mapToFormData(ValueObject $role, array $params = [])
    {
        $roleData = new RoleData(['role' => $role]);
        if (!$roleData->isNew()) {
            $roleData->identifier = $role->identifier;
        }

        return $roleData;
    }
}
