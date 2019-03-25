<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\Limitation\Mapper;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\RepositoryForms\Limitation\LimitationValueMapperInterface;

/**
 * Map possible or selected Translation Limitation values to a multiple selection form input.
 *
 * @see \eZ\Publish\API\Repository\Values\User\Limitation\TranslationLimitation
 */
class TranslationLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /**
     * @var \eZ\Publish\API\Repository\LanguageService
     */
    private $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * Get list of all possible translations.
     *
     * @return string[]
     */
    protected function getSelectionChoices(): array
    {
        $choices = [];
        foreach ($this->languageService->loadLanguages() as $language) {
            $choices[$language->languageCode] = $language->name;
        }

        return $choices;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\Limitation $limitation
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Language[]
     */
    public function mapLimitationValue(Limitation $limitation): array
    {
        $languages = $this->languageService->loadLanguageListByCode($limitation->limitationValues);

        return array_values($languages);
    }
}
