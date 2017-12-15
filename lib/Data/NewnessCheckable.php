<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data;

interface NewnessCheckable
{
    /**
     * Whether the Data object can be considered new.
     *
     * @return bool
     */
    public function isNew();
}
