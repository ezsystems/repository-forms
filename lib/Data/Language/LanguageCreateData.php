<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Language;

use eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct;
use EzSystems\RepositoryForms\Data\NewnessCheckable;

/**
 * @property-read \eZ\Publish\API\Repository\Values\Content\Language $language
 */
class LanguageCreateData extends LanguageCreateStruct implements NewnessCheckable
{
    use LanguageDataTrait;

    public function isNew()
    {
        return true;
    }
}
