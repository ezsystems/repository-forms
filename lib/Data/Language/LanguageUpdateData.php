<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Language;

use eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct;
use EzSystems\RepositoryForms\Data\NewnessChecker;
use EzSystems\RepositoryForms\Data\NewnessCheckable;

/**
 * Language Update Data struct.
 *
 * Extends LanguageCreateStruct as there is no LanguageUpdateStruct.
 * Downsides of this is that languageCode is not editable on update, and
 * that controller will need to execute calls to corresponding api's for name & enable/disable changes.
 *
 * @property-read \eZ\Publish\API\Repository\Values\Content\Language $language
 */
class LanguageUpdateData extends LanguageCreateStruct implements NewnessCheckable
{
    use LanguageDataTrait, NewnessChecker;

    /**
     * Returns the value of the property which can be considered as the value object identifier.
     *
     * @return string
     */
    protected function getIdentifierValue()
    {
        return $this->language->languageCode;
    }
}
