<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Limitation\Templating;

use eZ\Publish\API\Repository\Values\User\Limitation;

interface LimitationBlockRendererInterface
{
    /**
     * Returns limitation value in human readable format.
     *
     * @return string
     */
    public function renderLimitationValue(Limitation $limitation, array $parameters = []);
}
