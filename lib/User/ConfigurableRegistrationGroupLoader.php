<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\User;

use eZ\Publish\API\Repository\Repository;
use EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationGroupLoader as BaseConfigurableRegistrationGroupLoader;
use EzSystems\EzPlatformUser\ConfigResolver\RegistrationGroupLoader;
use EzSystems\RepositoryForms\ConfigResolver\ConfigurableSudoRepositoryLoader;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads the registration user group from a configured, injected group ID.
 * @deprecated Deprecated in 2.5 and will be removed in 3.0. Please use \EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationGroupLoader instead.
 */
class ConfigurableRegistrationGroupLoader extends ConfigurableSudoRepositoryLoader implements RegistrationGroupLoader
{
    /** @var \EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationGroupLoader */
    private $configurableRegistrationGroupLoader;

    /**
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param null $params
     * @param \EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationGroupLoader $configurableRegistrationGroupLoader
     */
    public function __construct(
        Repository $repository,
        $params = null,
        BaseConfigurableRegistrationGroupLoader $configurableRegistrationGroupLoader
    ) {
        $this->configurableRegistrationGroupLoader = $configurableRegistrationGroupLoader;
        parent::__construct($repository, $params);
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired('groupId');
    }

    /**
     * Loads a parent group.
     *
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public function loadGroup()
    {
        return $this->configurableRegistrationGroupLoader->loadGroup();
    }
}
