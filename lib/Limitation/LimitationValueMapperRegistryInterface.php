<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Limitation;

use EzSystems\RepositoryForms\Limitation\Exception\ValueMapperNotFoundException;

/**
 * Interface for Limitation value mappers registry.
 */
interface LimitationValueMapperRegistryInterface
{
    /**
     * Returns all available mappers.
     *
     * @return LimitationValueMapperInterface[]
     */
    public function getMappers();

    /**
     * Returns mapper corresponding to given Limitation Type.
     *
     * @throws ValueMapperNotFoundException If no mapper exists for $limitationType.
     *
     * @param string $limitationType
     * @return LimitationValueMapperInterface
     */
    public function getMapper($limitationType);

    /**
     * Checks if a mapper exists for given Limitation Type.
     *
     * @param string $limitationType
     * @return bool
     */
    public function hasMapper($limitationType);

    /**
     * Register mapper.
     *
     * @param string $limitationType Limitation identifier the mapper is meant for.
     */
    public function addMapper(LimitationValueMapperInterface $mapper, $limitationType);
}
