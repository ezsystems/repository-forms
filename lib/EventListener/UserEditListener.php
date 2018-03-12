<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\EventListener;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\RepositoryFormsBundle\Controller\UserController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Sets proper controller on content edit.
 */
class UserEditListener implements EventSubscriberInterface
{
    const CONTENT_CREATE_ROUTE = 'ez_content_create_no_draft';
    const CONTENT_EDIT_ROUTE = 'ez_content_draft_edit';
    const USER_FIELD_TYPE = 'ezuser';

    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var ContentService */
    protected $contentService;

    /** @var UserController */
    protected $userController;

    /**
     * @param ContentTypeService $contentTypeService
     * @param ContentService $contentService
     * @param UserController $userController
     */
    public function __construct(
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        UserController $userController
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->userController = $userController;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onControllerUserCreate', 20],
                ['onControllerUserEdit', 30],
            ],
        ];
    }

    public function onControllerUserCreate(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if (self::CONTENT_CREATE_ROUTE === $request->attributes->get('_route')) {
            $routeParams = $request->attributes->get('_route_params', ['contentTypeIdentifier' => null]);
            $contentTypeIdentifier = $routeParams['contentTypeIdentifier'];
            if (null !== $contentTypeIdentifier) {
                try {
                    $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
                    $isUserBasedContentType = $this->hasUserField($contentType);
                } catch (NotFoundException $e) {
                    return;
                }

                if (!$isUserBasedContentType) {
                    return;
                }

                $event->getRequest()->attributes->set('_controller', [$this->userController, 'createAction']);
                $event->setController([$this->userController, 'createAction']);
            }
        }
    }

    public function onControllerUserEdit(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if (self::CONTENT_EDIT_ROUTE === $request->attributes->get('_route')) {
            $routeParams = $request->attributes->get('_route_params', ['contentId' => null]);
            $contentId = $routeParams['contentId'];
            if (null !== $contentId) {
                try {
                    $content = $this->contentService->loadContent($contentId);
                    $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
                    $isUserBasedContentType = $this->hasUserField($contentType);
                } catch (NotFoundException $e) {
                    return;
                }

                if (!$isUserBasedContentType) {
                    return;
                }

                $event->getRequest()->attributes->set('_controller', [$this->userController, 'editAction']);
                $event->setController([$this->userController, 'editAction']);
            }
        }
    }

    /**
     * @param ContentType $contentType
     *
     * @return bool
     */
    private function hasUserField(ContentType $contentType): bool
    {
        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if (self::USER_FIELD_TYPE === $fieldDefinition->fieldTypeIdentifier) {
                return true;
            }
        }

        return false;
    }
}
