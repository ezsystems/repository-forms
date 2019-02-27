<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\ConfigResolver;

use Closure;
use eZ\Publish\API\Repository\Repository;
use OutOfBoundsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ** Use with care**.
 *
 * A repository data loader that uses the sudo() method.
 *
 * It comes with parameter handling, either by passing an array of (supported) options
 * to the constructor, or using the setParam() method.
 *
 * Implementations will call load() with a repository callback as an argument.
 * The repository can be accessed using getRepository().
 */
abstract class ConfigurableSudoRepositoryLoader
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var array */
    private $params;

    /**
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param array $params
     */
    public function __construct(Repository $repository, $params = [])
    {
        $this->repository = $repository;
        $this->params = $params;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
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

    /**
     * @param \Closure $callback
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function sudo(Closure $callback)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->params = $resolver->resolve($this->params);

        return $this->repository->sudo($callback);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    abstract protected function configureOptions(OptionsResolver $optionsResolver);
}
