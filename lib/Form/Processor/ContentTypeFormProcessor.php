<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Form\Processor;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class ContentTypeFormProcessor implements EventSubscriberInterface
{
    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var array
     */
    private $options;

    public function __construct(ContentTypeService $contentTypeService, RouterInterface $router, array $options = [])
    {
        $this->contentTypeService = $contentTypeService;
        $this->router = $router;
        $this->setOptions($options);
    }

    public function setOptions(array $options = [])
    {
        $this->options = $options + ['redirectRouteAfterPublish' => null];
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::CONTENT_TYPE_ADD_FIELD_DEFINITION => 'processAddFieldDefinition',
            RepositoryFormEvents::CONTENT_TYPE_PUBLISH => 'processPublishContentType',
        ];
    }

    public function processAddFieldDefinition(FormActionEvent $event)
    {
        $contentTypeDraft = $event->getData()->contentTypeDraft;
        $fieldTypeIdentifier = $event->getForm()->get('fieldTypeSelection')->getData();
        $fieldDefCreateStruct = new FieldDefinitionCreateStruct([
            'fieldTypeIdentifier' => $fieldTypeIdentifier,
            'identifier' => sprintf('new_%s_%d', $fieldTypeIdentifier, count($contentTypeDraft->fieldDefinitions) + 1),
            'names' => [$event->getLanguageCode() => 'New FieldDefinition'],
        ]);
        $this->contentTypeService->addFieldDefinition($contentTypeDraft, $fieldDefCreateStruct);
    }

    public function processPublishContentType(FormActionEvent $event)
    {
        $contentTypeDraft = $event->getData()->contentTypeDraft;
        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);
        if (isset($this->options['redirectRouteAfterPublish'])) {
            $event->setResponse(
                new RedirectResponse($this->router->generate($this->options['redirectRouteAfterPublish']))
            );
        }
    }
}
