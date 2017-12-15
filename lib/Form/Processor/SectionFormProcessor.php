<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Processor;

use eZ\Publish\API\Repository\SectionService;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SectionFormProcessor implements EventSubscriberInterface
{
    /**
     * @var SectionService
     */
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::SECTION_UPDATE => ['processUpdate', 10],
        ];
    }

    public function processUpdate(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\Section\SectionUpdateData|\EzSystems\RepositoryForms\Data\Section\SectionCreateData $sectionData */
        $sectionData = $event->getData();
        if ($sectionData->isNew()) {
            $section = $this->sectionService->createSection($sectionData);
        } else {
            $section = $this->sectionService->updateSection($sectionData->section, $sectionData);
        }

        $sectionData->setSection($section);
    }
}
