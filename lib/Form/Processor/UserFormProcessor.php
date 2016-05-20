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
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use EzSystems\RepositoryForms\Data\User\UserCreateData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listens for and processes RepositoryForm events: publish, remove draft, save draft...
 */
class UserFormProcessor implements EventSubscriberInterface
{
    /**
     * @var \eZ\Publish\API\Repository\UserService
     */
    private $userService;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    public function __construct(UserService $userService, RouterInterface $router)
    {
        $this->userService = $userService;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::CONTENT_PUBLISH => ['processPublish', 20],
        ];
    }

    public function processPublish(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\Content\ContentCreateData|\EzSystems\RepositoryForms\Data\Content\ContentUpdateData $data */
        $data = $event->getData();
        $form = $event->getForm();

        $user = $this->saveDraft($data, $form->getConfig()->getOption('languageCode'));

        // Redirect to the provided URL. Defaults to URLAlias of the published content.
        $redirectUrl = $form['redirectUrlAfterPublish']->getData() ?: $this->router->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            ['contentId' => $user->id],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $event->setResponse(new RedirectResponse($redirectUrl));
        $event->stopPropagation();
    }

    /**
     * Saves content draft corresponding to $data.
     * Depending on the nature of $data (create or update data), the draft will either be created or simply updated.
     *
     * @param ContentStruct|\EzSystems\RepositoryForms\Data\Content\ContentCreateData|\EzSystems\RepositoryForms\Data\Content\ContentUpdateData $data
     * @param $languageCode
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    private function saveDraft(UserCreateData $data, $languageCode)
    {
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($fieldData->getFieldTypeIdentifier() === 'ezuser') {
                print_r($data);
            } else {
                $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
            }
        }

        return $this->userService->createUser($data, $data->getParentGroups());
    }
}
