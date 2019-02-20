<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\EventListener;

use EzSystems\RepositoryForms\Event\FieldDefinitionSettingsTranslateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EzSelectionMultilingualOptions implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [FieldDefinitionSettingsTranslateEvent::NAME => 'setMultilingualOptions'];
    }

    public function setMultilingualOptions(FieldDefinitionSettingsTranslateEvent $event)
    {
        if ('ezselection' !== $event->getFieldTypeIdentifier()) {
            return;
        }

        $fieldSettings = $event->getFieldSettings();

        if (isset($fieldSettings['multilingualOptions'][$event->getBaseLanguageCode()])) {
            $fieldSettings['multilingualOptions'][$event->getTargetLanguageCode()] = $fieldSettings['multilingualOptions'][$event->getBaseLanguageCode()];
        }

        $event->setFieldSettings($fieldSettings);
    }
}
