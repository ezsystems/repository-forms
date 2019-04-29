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

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
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

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /**
     * @param UserService $userService
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        UserService $userService,
        ContentService $contentService,
        LocationService $locationService,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->userService = $userService;
        $this->urlGenerator = $urlGenerator;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
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

        $user = $this->contentService->createContent(
            $data,
            $this->createLocationCreateStructsFromUserGroups($data->getParentGroups())
        );
        $user = $this->contentService->publishVersion($user->versionInfo);

        $redirectUrl = $form['redirectUrlAfterPublish']->getData() ?: $this->urlGenerator->generate(
            '_ezpublishLocation',
            ['locationId' => $user->contentInfo->mainLocationId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $event->setResponse(new RedirectResponse($redirectUrl));
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\UserGroup[] $userGroups
     *
     * @return \eZ\Publish\API\Repository\Values\Content\LocationCreateStruct[]
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function createLocationCreateStructsFromUserGroups(array $userGroups): array
    {
        $locationCreateStructs = [];
        foreach ($userGroups as $parentGroup) {
            $parentGroup = $this->userService->loadUserGroup($parentGroup->id);
            if ($parentGroup->getVersionInfo()->getContentInfo()->mainLocationId !== null) {
                $locationCreateStructs[] = $this->locationService->newLocationCreateStruct(
                    $parentGroup->getVersionInfo()->getContentInfo()->mainLocationId
                );
            }
        }

        return $locationCreateStructs;
    }

    /**
     * @param UserCreateData $data
     * @param string $languageCode
     */
    private function setContentFields(UserCreateData $data, string $languageCode): void
    {
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }
    }
}
