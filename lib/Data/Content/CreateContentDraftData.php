<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Content;

use eZ\Publish\API\Repository\Values\ValueObject;

class CreateContentDraftData extends ValueObject
{
    public $contentId;

    public $fromVersionNo;

    public $fromLanguage;

    public $toLanguage;
}
