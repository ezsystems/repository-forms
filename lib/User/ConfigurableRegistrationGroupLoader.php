<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\User;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads the registration user group from a configured, injected group ID.
 */
class ConfigurableRegistrationGroupLoader extends ConfigurableSudoRepositoryLoader implements RegistrationGroupLoader
{
    public function loadGroup()
    {
        return $this->sudo(
            function () {
                return $this->getRepository()
                    ->getUserService()
                    ->loadUserGroup(
                        $this->getParam('groupId')
                    );
            }
        );
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired('groupId');
    }
}
