<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryFormsBundle;

use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\FieldTypeFormMapperPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\LimitationFormMapperPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\ViewBuildersPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser\ContentEditView;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsRepositoryFormsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FieldTypeFormMapperPass());
        $container->addCompilerPass(new LimitationFormMapperPass());
        $container->addCompilerPass(new ViewBuildersPass());

        /**
         * @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension
         */
        $eZExtension = $container->getExtension('ezpublish');
        $eZExtension->addConfigParser(new ContentEditView());
    }
}
