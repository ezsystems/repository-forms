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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var array
     */
    private $options;

    public function __construct(ContentTypeService $contentTypeService, RouterInterface $router, TranslatorInterface $translator, Session $session, array $options = [])
    {
        $this->contentTypeService = $contentTypeService;
        $this->router = $router;
        $this->translator = $translator;
        $this->session = $session;
        $this->setOptions($options);
    }

    public function setOptions(array $options = [])
    {
        $this->options = $options + ['redirectRouteAfterPublish' => null];
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::CONTENT_TYPE_UPDATE => 'processDefaultAction',
            RepositoryFormEvents::CONTENT_TYPE_ADD_FIELD_DEFINITION => 'processAddFieldDefinition',
            RepositoryFormEvents::CONTENT_TYPE_REMOVE_FIELD_DEFINITION => 'processRemoveFieldDefinition',
            RepositoryFormEvents::CONTENT_TYPE_PUBLISH => 'processPublishContentType',
            RepositoryFormEvents::CONTENT_TYPE_REMOVE_DRAFT => 'processRemoveContentTypeDraft',
        ];
    }

    public function processDefaultAction(FormActionEvent $event)
    {
        // Always update FieldDefinitions and ContentTypeDraft
        /** @var \EzSystems\RepositoryForms\Data\ContentTypeData $contentTypeData */
        $contentTypeData = $event->getData();
        $contentTypeDraft = $contentTypeData->contentTypeDraft;
        foreach ($contentTypeData->fieldDefinitionsData as $fieldDefData) {
            $this->contentTypeService->updateFieldDefinition($contentTypeDraft, $fieldDefData->fieldDefinition, $fieldDefData);
        }
        $this->contentTypeService->updateContentTypeDraft($contentTypeDraft, $contentTypeData);
        $this->addNotification('content_type.notification.draft_updated');
    }

    public function processAddFieldDefinition(FormActionEvent $event)
    {
        $contentTypeDraft = $event->getData()->contentTypeDraft;
        $fieldTypeIdentifier = $event->getForm()->get('fieldTypeSelection')->getData();
        $fieldDefCreateStruct = new FieldDefinitionCreateStruct([
            'fieldTypeIdentifier' => $fieldTypeIdentifier,
            'identifier' => sprintf('new_%s_%d', $fieldTypeIdentifier, count($contentTypeDraft->fieldDefinitions) + 1),
            'names' => [$event->getOption('languageCode') => 'New FieldDefinition'],
        ]);
        $this->contentTypeService->addFieldDefinition($contentTypeDraft, $fieldDefCreateStruct);
    }

    public function processRemoveFieldDefinition(FormActionEvent $event)
    {
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft */
        $contentTypeDraft = $event->getData()->contentTypeDraft;

        // Accessing FieldDefinition user selection through the form and not the data,
        // as "selected" is not a property of FieldDefinitionData.
        /** @var \Symfony\Component\Form\FormInterface $fieldDefForm */
        foreach ($event->getForm()->get('fieldDefinitionsData') as $fieldDefForm) {
            if ($fieldDefForm->get('selected')->getData() === true) {
                $this->contentTypeService->removeFieldDefinition($contentTypeDraft, $fieldDefForm->getData()->fieldDefinition);
            }
        }
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

        $this->addNotification('content_type.notification.published');
    }

    public function processRemoveContentTypeDraft(FormActionEvent $event)
    {
        $contentTypeDraft = $event->getData()->contentTypeDraft;
        $this->contentTypeService->deleteContentType($contentTypeDraft);
        if (isset($this->options['redirectRouteAfterPublish'])) {
            $event->setResponse(
                new RedirectResponse($this->router->generate($this->options['redirectRouteAfterPublish']))
            );
        }

        $this->addNotification('content_type.notification.draft_removed');
    }

    /**
     * Pushes a flash notification to session.
     *
     * @param string $message
     */
    private function addNotification($message, array $params = [])
    {
        $this->session->getFlashBag()->set(
            'notification',
            $this->translator->trans($message, $params, 'ezrepoforms_content_type')
        );
    }
}
