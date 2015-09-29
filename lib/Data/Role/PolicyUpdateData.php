<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\Role;

use eZ\Publish\Core\Repository\Values\User\PolicyUpdateStruct;
use EzSystems\RepositoryForms\Data\NewnessChecker;

/**
 * @property-read \eZ\Publish\API\Repository\Values\User\Policy $policy
 * @property-read \eZ\Publish\API\Repository\Values\User\RoleDraft $roleDraft
 */
class PolicyUpdateData extends PolicyUpdateStruct
{
    use PolicyDataTrait, NewnessChecker;

    protected function getIdentifierValue()
    {
        return $this->policy->module;
    }
}
