<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

// Skip autoloading if already done by phpunit alias (including from meta repo if this is vendor)
if (defined('PHPUNIT_COMPOSER_INSTALL')) {
    return;
}

$file = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies using composer to run the test suite.');
}

$autoload = require_once $file;
