<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Form\Processor;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use EzSystems\RepositoryForms\Data\User\UserCreateData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listens for and processes RepositoryForm events: publish, remove draft, save draft...
 */
class UserRegisterFormProcessor implements EventSubscriberInterface
{
    /**
     * @var \eZ\Publish\API\Repository\UserService
     */
    private $userService;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(Repository $repository, UserService $userService, RouterInterface $router)
    {
        $this->userService = $userService;
        $this->router = $router;
        $this->repository = $repository;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::CONTENT_PUBLISH => ['processPublish', 20],
        ];
    }

    public function processPublish(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\User\UserCreateData $data */
        $data = $event->getData();
        $form = $event->getForm();

        $this->saveDraft($data, $form->getConfig()->getOption('languageCode'));

        $redirectUrl = $this->router->generate('ez_user_register_confirmation');
        $event->setResponse(new RedirectResponse($redirectUrl));
        $event->stopPropagation();
    }

    /**
     * Saves content draft corresponding to $data.
     * Depending on the nature of $data (create or update data), the draft will either be created or simply updated.
     *
     * @param ContentStruct|\EzSystems\RepositoryForms\Data\User\UserCreateData $data
     * @param $languageCode
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    private function saveDraft(UserCreateData $data, $languageCode)
    {
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($fieldData->getFieldTypeIdentifier() !== 'ezuser') {
                $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
            }
        }

        return $this->repository->sudo(
            function () use ($data) {
                return $this->userService->createUser($data, $data->getParentGroups());
            }
        );
    }
}
