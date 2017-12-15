<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Role;

use eZ\Publish\API\Repository\Values\User\RoleUpdateStruct;
use EzSystems\RepositoryForms\Data\NewnessChecker;
use EzSystems\RepositoryForms\Data\NewnessCheckable;

/**
 * Base data class for ContentType update form, with FieldDefinitions data and ContentTypeDraft.
 *
 * @property-read \eZ\Publish\API\Repository\Values\User\RoleDraft $roleDraft
 */
class RoleData extends RoleUpdateStruct implements NewnessCheckable
{
    /**
     * Trait which provides isNew(), and mandates getIdentifier().
     */
    use NewnessChecker;

    /**
     * @var \eZ\Publish\API\Repository\Values\User\RoleDraft
     */
    protected $roleDraft;

    protected function getIdentifierValue()
    {
        return $this->roleDraft->identifier;
    }
}
