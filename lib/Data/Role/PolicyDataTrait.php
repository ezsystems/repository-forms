<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\Role;

use eZ\Publish\API\Repository\Values\User\Policy;

trait PolicyDataTrait
{
    /**
     * @var Policy
     */
    protected $policy;

    /**
     * @var \eZ\Publish\API\Repository\Values\User\RoleDraft
     */
    protected $roleDraft;

    /**
     * Combination of module + function as a single string.
     * Example: "content|read".
     *
     * @var string
     */
    public $moduleFunction;

    public function setPolicy(Policy $policy)
    {
        $this->policy = $policy;
    }

    public function getId()
    {
        return $this->policy ? $this->policy->id : null;
    }
}
