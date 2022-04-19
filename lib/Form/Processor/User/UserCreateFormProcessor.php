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

use eZ\Publish\API\Repository\UserService;
use EzSystems\RepositoryForms\Data\User\UserCreateData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listens for and processes User create events.
 */
class UserCreateFormProcessor implements EventSubscriberInterface
{
    /** @var UserService */
    private $userService;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        UserService $userService,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->userService = $userService;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::USER_CREATE => ['processCreate', 20],
        ];
    }

    public function processCreate(FormActionEvent $event)
    {
        $data = $data = $event->getData();

        if (!$data instanceof UserCreateData) {
            return;
        }

        $form = $event->getForm();
        $languageCode = $form->getConfig()->getOption('languageCode');

        $this->setContentFields($data, $languageCode);
        $user = $this->userService->createUser($data, $data->getParentGroups());

        $redirectUrl = $form['redirectUrlAfterPublish']->getData() ?: $this->urlGenerator->generate(
            '_ezpublishLocation',
            ['locationId' => $user->contentInfo->mainLocationId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $event->setResponse(new RedirectResponse($redirectUrl));
    }

    private function setContentFields(UserCreateData $data, string $languageCode): void
    {
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ('ezuser' === $fieldData->getFieldTypeIdentifier()) {
                continue;
            }

            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }
    }
}
