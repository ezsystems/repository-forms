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
        // TODO: When we have role versioning, save draft here.
        // For now, processSaveRole takes care of saving. Follow-up: EZP-24701
        //$this->addNotification('role.notification.draft_saved');
    }

    public function processSaveRole(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\RoleData $roleData */
        $roleData = $event->getData();
        $this->roleService->updateRole($roleData->role, $roleData);
    }

    public function processRemoveDraft(FormActionEvent $event)
    {
        $role = $event->getData()->role;
        // TODO: This is just a temporary implementation of draft removal. To be done properly in follow-up: EZP-24701
        if (preg_match('/^__new__[a-z0-9]{32}$/', $role->identifier) === 1) {
            $this->roleService->deleterole($role);
        }
    }
}
