<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\User;

use Closure;
use eZ\Publish\API\Repository\Repository;
use OutOfBoundsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A repository data loader that uses the sudo() method.
 *
 * It comes with parameter handling, either by passing an array of (supported) options
 * to the constructor, or using the setParam() method.
 *
 * Implementations will call load() with a repository callback as an argument.
 * The repository can be accessed using getRepository().
 *
 * ** Use with care**.
 * @deprecated Deprecated in 2.5 and will be removed in 3.0. Please use \EzSystems\RepositoryForms\ConfigResolver\ConfigurableSudoRepositoryLoader instead.
 */
abstract class ConfigurableSudoRepositoryLoader
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

    protected function getParam($name)
    {
        if (!isset($this->params[$name])) {
            throw new OutOfBoundsException("No such param '$name'");
        }

        return $this->params[$name];
    }

    /**
     * @return Repository
     */
    protected function getRepository()
    {
        return $this->repository;
    }

    protected function sudo(Closure $callback)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->params = $resolver->resolve($this->params);

        return $this->repository->sudo($callback);
    }

    abstract protected function configureOptions(OptionsResolver $optionsResolver);
}
