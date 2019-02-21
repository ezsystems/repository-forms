<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\User;

use eZ\Publish\API\Repository\Repository;
use EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationContentTypeLoader as BaseConfigurableRegistrationContentTypeLoader;
use EzSystems\EzPlatformUser\ConfigResolver\RegistrationContentTypeLoader;
use EzSystems\RepositoryForms\ConfigResolver\ConfigurableSudoRepositoryLoader;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads the registration content type from a configured, injected content type identifier.
 * @deprecated Deprecated in 2.5 and will be removed in 3.0. Please use \EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationContentTypeLoader instead.
 */
class ConfigurableRegistrationContentTypeLoader extends ConfigurableSudoRepositoryLoader implements RegistrationContentTypeLoader
{
    /** @var \EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationContentTypeLoader */
    private $configurableRegistrationContentTypeLoader;

    /**
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param null $params
     * @param \EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationContentTypeLoader $configurableRegistrationContentTypeLoader
     */
    public function __construct(
        Repository $repository,
        $params = null,
        BaseConfigurableRegistrationContentTypeLoader $configurableRegistrationContentTypeLoader
    ) {
        $this->configurableRegistrationContentTypeLoader = $configurableRegistrationContentTypeLoader;
        parent::__construct($repository, $params);
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired('contentTypeIdentifier');
    }

    /**
     * Gets the Content Type used by user registration.
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    public function loadContentType()
    {
        return $this->configurableRegistrationContentTypeLoader->loadContentType();
    }
}
