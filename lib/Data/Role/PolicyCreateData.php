<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Role;

use eZ\Publish\Core\Repository\Values\User\PolicyCreateStruct;
use EzSystems\RepositoryForms\Data\NewnessCheckable;

/**
 * @property-read \eZ\Publish\API\Repository\Values\User\PolicyDraft $policyDraft
 * @property-read \eZ\Publish\API\Repository\Values\User\RoleDraft $roleDraft
 * @property-read \eZ\Publish\API\Repository\Values\User\Role $initialRole
 * @property-read \eZ\Publish\API\Repository\Values\User\Limitation[] $limitationsData
 */
class PolicyCreateData extends PolicyCreateStruct implements NewnessCheckable
{
    use PolicyDataTrait;

    public function isNew()
    {
        return true;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getFunction()
    {
        return $this->function;
    }
}
