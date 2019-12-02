<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

$config = EzSystems\EzPlatformCodeStyle\PhpCsFixer\EzPlatformInternalConfigFactory::build();

$config->setFinder(
    PhpCsFixer\Finder::create()
        ->in(__DIR__)
        ->exclude([
        'bin/.travis',
        'docs',
        'vendor',
        ])
        ->files()->name('*.php')
);

return $config;
