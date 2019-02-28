<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Mapper;

use EzSystems\EzPlatformUser\Form\DataMapper\UserRegisterMapper as BaseUserRegisterMapper;

/**
 * Form data mapper for user creation.
 * @deprecated Deprecated in 2.5 and will be removed in 3.0. Please use EzSystems\EzPlatformUser\Form\DataMapper\UserRegisterMapper instead.
 */
class UserRegisterMapper
{
    /** @var \EzSystems\EzPlatformUser\Form\DataMapper\UserRegisterMapper */
    private $baseUserRegisterMapper;

    /**
     * @param \EzSystems\EzPlatformUser\Form\DataMapper\UserRegisterMapper $baseUserRegisterMapper
     */
    public function __construct(
        BaseUserRegisterMapper $baseUserRegisterMapper
    ) {
        $this->baseUserRegisterMapper = $baseUserRegisterMapper;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setParam($name, $value)
    {
        $this->baseUserRegisterMapper->setParam($name, $value);
    }

    /**
     * @return \EzSystems\EzPlatformUser\Form\Data\UserRegisterData
     */
    public function mapToFormData()
    {
        return $this->baseUserRegisterMapper->mapToFormData();
    }
}
