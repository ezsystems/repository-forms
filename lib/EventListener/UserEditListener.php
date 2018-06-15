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
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\RepositoryFormsBundle\Controller\UserController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;

/**
 * Sets proper controller on content edit.
 */
class UserEditListener implements EventSubscriberInterface
{
    const CONTENT_CREATE_ROUTE = 'ez_content_create_no_draft';
    const CONTENT_EDIT_ROUTE = 'ez_content_draft_edit';
    const CONTENT_BASE_EDIT_ROUTE = 'ezplatform.content.edit';
    const CONTENT_BASE_CREATE_ROUTE = 'ezplatform.content.create';
    const USER_FIELD_TYPE = 'ezuser';

    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var ContentService */
    protected $contentService;

    /** @var UserController */
    protected $userController;

    /** @var \Symfony\Component\Routing\Router */
    private $router;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\RepositoryFormsBundle\Controller\UserController $userController
     * @param \Symfony\Component\Routing\Router $router
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     */
    public function __construct(
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        UserController $userController,
        Router $router,
        FormFactory $formFactory
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->userController = $userController;
        $this->router = $router;
        $this->formFactory = $formFactory;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['beforeUserControllerCreate', -20],
                ['beforeUserControllerEdit', -30],
            ],
            KernelEvents::CONTROLLER => [
                ['onControllerUserCreate', 20],
                ['onControllerUserEdit', 30],
            ],
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     *
     * @throws \Exception
     */
    public function beforeUserControllerCreate(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (self::CONTENT_BASE_CREATE_ROUTE === $request->attributes->get('_route')) {
            $form = $this->formFactory->contentEdit(null, 'content_create');
            $form->handleRequest($request);

            if ($form->isValid()) {
                /** @var \EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData $data */
                $data = $form->getData();
                $contentType = $data->getContentType();
                $isUserBasedContentType = $this->hasUserField($contentType);

                if (!$isUserBasedContentType) {
                    return;
                }

                $response = new RedirectResponse(
                    $this->router->generate('ez_content_create_no_draft', [
                        'contentTypeIdentifier' => $contentType->identifier,
                        'language' => $data->getLanguage()->languageCode,
                        'parentLocationId' => $data->getParentLocation()->id,
                    ])
                );
                $event->setResponse($response);
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     *
     * @throws \Exception
     */
    public function beforeUserControllerEdit(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (self::CONTENT_BASE_EDIT_ROUTE === $request->attributes->get('_route')) {
            $form = $this->formFactory->contentEdit(null, 'content_edit');
            $form->handleRequest($request);

            if ($form->isValid()) {
                /** @var \EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData $data */
                $data = $form->getData();
                $location = $data->getLocation();
                $contentId = $location->contentInfo->id;

                try {
                    $contentType = $this->contentTypeService->loadContentType($location->contentInfo->contentTypeId);
                    $isUserBasedContentType = $this->hasUserField($contentType);
                } catch (NotFoundException $e) {
                    return;
                }

                if (!$isUserBasedContentType) {
                    return;
                }

                $response = new RedirectResponse(
                    $this->router->generate('ez_content_draft_edit', [
                        'contentId' => $contentId,
                        'versionNo' => $data->getVersionInfo()->versionNo,
                        'language' => $data->getLanguage()->languageCode,
                        'locationId' => $location->id,
                    ])
                );
                $event->setResponse($response);
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     */
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

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
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
