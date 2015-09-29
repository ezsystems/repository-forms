<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\Role;

use eZ\Publish\Core\Repository\Values\User\PolicyCreateStruct;
use EzSystems\RepositoryForms\Data\NewsnessCheckable;

/**
 * @property-read \eZ\Publish\API\Repository\Values\User\Policy $policy
 * @property-read \eZ\Publish\API\Repository\Values\User\RoleDraft $roleDraft
 */
class PolicyCreateData extends PolicyCreateStruct implements NewsnessCheckable
{
    use PolicyDataTrait;

    public function isNew()
    {
        return true;
    }
}
