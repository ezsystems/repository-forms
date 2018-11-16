<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\User;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\API\Repository\Values\User\UserUpdateStruct;
use EzSystems\RepositoryForms\Data\Content\ContentData;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\NewnessCheckable;

/**
 * @property-read FieldData[] $fieldsData
 * @property-read User $user
 */
class UserUpdateData extends UserUpdateStruct implements NewnessCheckable
{
    use ContentData;

    /**
     * @var User
     */
    public $user;

    /**
     * @var ContentType
     */
    public $contentType;

    public function isNew()
    {
        return false;
    }
}
