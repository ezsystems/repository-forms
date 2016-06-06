<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\UserRegister;

use eZ\Publish\API\Repository\Repository;
use EzSystems\RepositoryForms\Data\User\UserCreateData;
use InvalidArgumentException;

/**
 * Loads the registration user group from a configured, injected group ID.
 */
class ConfigurableRegistrationGroupLoader implements RegistrationGroupLoader
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var string
     */
    private $groupId;

    public function __construct(Repository $repository, $groupId = null)
    {
        $this->repository = $repository;
        $this->groupId = $groupId;
    }

    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    public function getParentGroup(UserCreateData $userCreateData)
    {
        if ($this->groupId === null) {
            throw new InvalidArgumentException('groupId needs to be set');
        }

        return $this->repository->sudo(
            function () {
                return $this->repository->getUserService()->loadUserGroup($this->groupId);
            }
        );
    }
}
