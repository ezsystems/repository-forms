<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Processor;

use eZ\Publish\API\Repository\LanguageService;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LanguageFormProcessor implements EventSubscriberInterface
{
    /**
     * @var LanguageService
     */
    private $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::LANGUAGE_UPDATE => ['processUpdate', 10],
        ];
    }

    public function processUpdate(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\Language\LanguageUpdateData|\EzSystems\RepositoryForms\Data\Language\LanguageCreateData $languageData */
        $languageData = $event->getData();
        if ($languageData->isNew()) {
            $language = $this->languageService->createLanguage($languageData);
        } else {
            // As there is no update struct for language service, we first update name if it has changed
            $language = $languageData->language;
            if ($languageData->name !== $language->name) {
                $language = $this->languageService->updateLanguageName($language, $languageData->name);
            }

            // check if we should enable / disable language
            if ($languageData->enabled !== $language->enabled) {
                if ($languageData->enabled) {
                    $language = $this->languageService->enableLanguage($language);
                } else {
                    $language = $this->languageService->disableLanguage($language);
                }
            }
        }

        $languageData->setLanguage($language);
    }
}
