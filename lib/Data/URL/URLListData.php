<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\URL;

use eZ\Publish\API\Repository\Values\ValueObject;

class URLListData extends ValueObject
{
    /**
     * @var string|null
     */
    public $searchQuery;

    /**
     * @var bool|null
     */
    public $status;

    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var int
     */
    public $limit = 10;
}
