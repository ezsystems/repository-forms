<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Processor\User;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\User;
use EzSystems\RepositoryForms\Data\User\UserRegisterData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listens for and processes User register events.
 */
class UserRegisterFormProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Repository */
    private $repository;

    public function __construct(Repository $repository, UserService $userService, RouterInterface $router)
    {
        $this->userService = $userService;
        $this->urlGenerator = $router;
        $this->repository = $repository;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::USER_REGISTER => ['processRegister', 20],
        ];
    }

    public function processRegister(FormActionEvent $event)
    {
        /** @var UserRegisterData $data */
        if (!($data = $event->getData()) instanceof UserRegisterData) {
            return;
        }
        $form = $event->getForm();

        $this->createUser($data, $form->getConfig()->getOption('languageCode'));

        $redirectUrl = $this->urlGenerator->generate('ez_user_register_confirmation');
        $event->setResponse(new RedirectResponse($redirectUrl));
        $event->stopPropagation();
    }

    /**
     * @param UserRegisterData $data
     * @param $languageCode
     *
     * @return User
     */
    private function createUser(UserRegisterData $data, $languageCode)
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
