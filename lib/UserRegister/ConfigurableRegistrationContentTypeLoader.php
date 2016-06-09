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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads the registration content type from a configured, injected content type identifier.
 */
class ConfigurableRegistrationContentTypeLoader implements RegistrationContentTypeLoader
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var array
     */
    private $params = [];

    public function __construct(Repository $repository, $params = null)
    {
        $this->repository = $repository;
        $this->params = $params;
    }

    public function setParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    public function loadContentType()
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->params = $resolver->resolve($this->params);

        return $this->repository->sudo(
            function () {
                return
                    $this->repository
                        ->getContentTypeService()
                        ->loadContentTypeByIdentifier(
                            $this->params['contentTypeIdentifier']
                        );
            }
        );
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired('contentTypeIdentifier');
    }
}
