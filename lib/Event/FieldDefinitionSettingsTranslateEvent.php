<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\Event;

use Symfony\Component\EventDispatcher\Event;

class FieldDefinitionSettingsTranslateEvent extends Event
{
    /**
     * Triggered when contentTypeData is created from contentTypeDraft.
     */
    public const NAME = 'field_definition.settings.translate';

    /** @var string */
    private $fieldTypeIdentifier;

    /** @var array */
    private $fieldSettings;

    /** @var string */
    private $baseLanguageCode;

    /** @var string */
    private $targetLanguageCode;

    /**
     * @param string $fieldTypeIdentifier
     * @param array $fieldSettings
     * @param string $baseLanguageCode
     * @param string $targetLanguageCode
     */
    public function __construct(
        string $fieldTypeIdentifier,
        array $fieldSettings,
        string $baseLanguageCode,
        string $targetLanguageCode
    ) {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->fieldSettings = $fieldSettings;
        $this->baseLanguageCode = $baseLanguageCode;
        $this->targetLanguageCode = $targetLanguageCode;
    }

    /**
     * @return array
     */
    public function getFieldSettings(): array
    {
        return $this->fieldSettings;
    }

    /**
     * @return string
     */
    public function getBaseLanguageCode(): string
    {
        return $this->baseLanguageCode;
    }

    /**
     * @return string
     */
    public function getTargetLanguageCode(): string
    {
        return $this->targetLanguageCode;
    }

    /**
     * @return string
     */
    public function getFieldTypeIdentifier(): string
    {
        return $this->fieldTypeIdentifier;
    }

    /**
     * @param array $fieldSettings
     */
    public function setFieldSettings(array $fieldSettings): void
    {
        $this->fieldSettings = $fieldSettings;
    }
}
