<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\UserRegister;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads the registration content type from a configured, injected content type identifier.
 */
class ConfigurableRegistrationContentTypeLoader
    extends ConfigurableSudoRepositoryLoader
    implements RegistrationContentTypeLoader
{
    public function loadContentType()
    {
        return $this->sudo(
            function () {
                return
                    $this->getRepository()
                        ->getContentTypeService()
                        ->loadContentTypeByIdentifier(
                            $this->getParam('contentTypeIdentifier')
                        );
            }
        );
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired('contentTypeIdentifier');
    }
}
