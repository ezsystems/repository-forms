<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Form\Processor;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\Core\Repository\Values\User\RoleDraft;
use EzSystems\RepositoryForms\Data\Role\RoleData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use EzSystems\RepositoryForms\Form\Processor\RoleFormProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormInterface;

class RoleFormProcessorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $roleService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $router;

    /**
     * @var RoleFormProcessor
     */
    private $formProcessor;

    protected function setUp()
    {
        parent::setUp();
        $this->roleService = $this->createMock(RoleService::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->formProcessor = new RoleFormProcessor($this->roleService, $this->router);
    }

    public function testSubscribedEvents()
    {
        self::assertSame([
            RepositoryFormEvents::ROLE_UPDATE => ['processDefaultAction'],
            RepositoryFormEvents::ROLE_SAVE => ['processSaveRole'],
            RepositoryFormEvents::ROLE_REMOVE_DRAFT => ['processRemoveDraft'],
        ], RoleFormProcessor::getSubscribedEvents());
    }

    public function testProcessDefaultAction()
    {
        $roleDraft = new RoleDraft();
        $roleData = new RoleData(['roleDraft' => $roleDraft]);
        $event = new FormActionEvent($this->createMock(FormInterface::class), $roleData, null);

        $this->roleService
            ->expects($this->at(0))
            ->method('updateRoleDraft')
            ->with($roleDraft, $roleData);
        $this->roleService
            ->expects($this->at(1))
            ->method('publishRoleDraft')
            ->with($roleDraft);

        $this->formProcessor->processDefaultAction($event);
    }

    public function testProcessSaveRole()
    {
        $roleDraft = new RoleDraft();
        $roleData = new RoleData(['roleDraft' => $roleDraft]);
        $event = new FormActionEvent($this->createMock(FormInterface::class), $roleData, 'saveRole');

        $this->roleService
            ->expects($this->at(0))
            ->method('updateRoleDraft')
            ->with($roleDraft, $roleData);
        $this->roleService
            ->expects($this->at(1))
            ->method('publishRoleDraft')
            ->with($roleDraft);

        $this->formProcessor->processSaveRole($event);
    }

    public function testProcessRemoveDraft()
    {
        $roleDraft = new RoleDraft();
        $roleData = new RoleData(['roleDraft' => $roleDraft]);
        $event = new FormActionEvent($this->createMock(FormInterface::class), $roleData, 'removeDraft');

        $this->roleService
            ->expects($this->once())
            ->method('deleteRoleDraft')
            ->with($roleDraft);

        $this->formProcessor->processRemoveDraft($event);
    }
}
