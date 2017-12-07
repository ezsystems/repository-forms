<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\User;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;

/**
 * Loads the content type used by user registration.
 */
interface RegistrationContentTypeLoader
{
    /**
     * Gets the Content Type used by user registration.
     *
     * @return ContentType
     */
    public function loadContentType();
}
