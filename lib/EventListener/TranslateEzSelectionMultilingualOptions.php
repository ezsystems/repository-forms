<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\EventListener;

use EzSystems\RepositoryForms\Event\FieldDefinitionMappingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TranslateEzSelectionMultilingualOptions implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [FieldDefinitionMappingEvent::NAME => ['setMultilingualOptions', 30]];
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FieldDefinitionMappingEvent $event
     */
    public function setMultilingualOptions(FieldDefinitionMappingEvent $event): void
    {
        $fieldDefinition = $event->getFieldDefinitionData()->fieldDefinition;
        if ('ezselection' !== $fieldDefinition->fieldTypeIdentifier) {
            return;
        }

        $baseLanguage = $event->getBaseLanguage();
        $targetLanguage = $event->getTargetLanguage();

        if (null === $baseLanguage || null === $targetLanguage) {
            return;
        }

        $fieldDefinitionData = $event->getFieldDefinitionData();
        $fieldSettings = $fieldDefinitionData->fieldSettings;

        if (isset($fieldSettings['multilingualOptions'][$baseLanguage->languageCode])) {
            $fieldSettings['multilingualOptions'][$targetLanguage->languageCode] = $fieldSettings['multilingualOptions'][$baseLanguage->languageCode];
        }

        $fieldDefinitionData->fieldSettings = $fieldSettings;

        $event->setFieldDefinitionData($fieldDefinitionData);
    }
}
