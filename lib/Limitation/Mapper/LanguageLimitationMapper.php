<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Limitation\Mapper;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\RepositoryForms\Limitation\LimitationValueMapperInterface;

class LanguageLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /**
     * @var LanguageService
     */
    private $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    protected function getSelectionChoices()
    {
        $choices = [];
        foreach ($this->languageService->loadLanguages() as $language) {
            $choices[$language->languageCode] = $language->name;
        }

        return $choices;
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];

        foreach ($limitation->limitationValues as $languageCode) {
            $values[] = $this->languageService->loadLanguage($languageCode);
        }

        return $values;
    }
}
