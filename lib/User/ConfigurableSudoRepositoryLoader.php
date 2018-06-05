<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\User;

use EzSystems\RepositoryForms\ConfigResolver\ConfigurableSudoRepositoryLoader as BaseConfigurableSudoRepositoryLoader;
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
 * @deprecated Deprecated in 1.1 and will be removed in 2.0. Please use \EzSystems\RepositoryForms\ConfigResolver\ConfigurableSudoRepositoryLoader instead.
 */
abstract class ConfigurableSudoRepositoryLoader extends BaseConfigurableSudoRepositoryLoader
{
}
