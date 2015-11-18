<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Processor;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ContentFormProcessor implements EventSubscriberInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    public function __construct(ContentService $contentService, RouterInterface $router)
    {
        $this->contentService = $contentService;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::CONTENT_PUBLISH => ['processPublish', 10],
        ];
    }

    public function processPublish(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\Content\ContentCreateData|\EzSystems\RepositoryForms\Data\Content\ContentUpdateData $data */
        $data = $event->getData();
        $form = $event->getForm();

        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $data->setField($fieldDefIdentifier, $fieldData->value, $form->getConfig()->getOption('languageCode'));
        }

        if ($data->isNew()) {
            $contentDraft = $this->contentService->createContent($data, $data->getLocationStructs());
        } else {
            $contentDraft = $this->contentService->updateContent($data->contentDraft->getVersionInfo(), $data);
        }

        $content = $this->contentService->publishVersion($contentDraft->versionInfo);

        $url = $this->router->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            ['contentId' => $content->id],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $event->setResponse(new RedirectResponse($url));
    }
}
