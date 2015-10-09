<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Processor;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\RoleDraft;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoleFormProcessor implements EventSubscriberInterface
{
    /**
     * @var RoleService
     */
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::ROLE_UPDATE => ['processDefaultAction'],
            RepositoryFormEvents::ROLE_SAVE => ['processSaveRole'],
            RepositoryFormEvents::ROLE_REMOVE_DRAFT => ['processRemoveDraft'],
        ];
    }

    public function processDefaultAction(FormActionEvent $event)
    {
        // Don't update anything if we just want to cancel the draft.
        if ($event->getClickedButton() === 'removeDraft') {
            return;
        }

        /** @var \EzSystems\RepositoryForms\Data\Role\RoleData $roleData */
        $roleData = $event->getData();
        $roleDraft = $roleData->roleDraft;
        $this->roleService->updateRoleDraft($roleDraft, $roleData);
    }

    public function processSaveRole(FormActionEvent $event)
    {
        /** @var RoleDraft $roleDraft */
        $roleDraft = $event->getData()->roleDraft;
        $this->roleService->publishRoleDraft($roleDraft);
    }

    public function processRemoveDraft(FormActionEvent $event)
    {
        /** @var RoleDraft $roleDraft */
        $roleDraft = $event->getData()->roleDraft;
        $this->roleService->deleteRoleDraft($roleDraft);
    }
}
