<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Form\Processor;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\Core\Repository\Values\User\Policy;
use eZ\Publish\Core\Repository\Values\User\PolicyDraft;
use eZ\Publish\Core\Repository\Values\User\Role;
use eZ\Publish\Core\Repository\Values\User\RoleDraft;
use EzSystems\RepositoryForms\Data\Mapper\PolicyMapper;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use EzSystems\RepositoryForms\Form\Processor\PolicyFormProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

class PolicyFormProcessorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $roleService;

    /**
     * @var PolicyFormProcessor
     */
    private $processor;

    protected function setUp()
    {
        parent::setUp();
        $this->roleService = $this->createMock(RoleService::class);
        $this->processor = new PolicyFormProcessor($this->roleService);
    }

    public function testGetSubscribedEvents()
    {
        self::assertSame(
            [
                RepositoryFormEvents::POLICY_UPDATE => 'processUpdatePolicy',
                RepositoryFormEvents::POLICY_SAVE => 'processSavePolicy',
                RepositoryFormEvents::POLICY_REMOVE_DRAFT => 'processRemoveDraft',
            ],
            PolicyFormProcessor::getSubscribedEvents()
        );
    }

    public function testProcessCreate()
    {
        $existingPolicy = new PolicyDraft(['innerPolicy' => new Policy(['id' => 123])]);
        $policy = new PolicyDraft(['innerPolicy' => new Policy()]);
        $initialRole = new Role(['policies' => [$existingPolicy]]);
        $roleDraft = new RoleDraft(['innerRole' => $initialRole]);
        $data = (new PolicyMapper())->mapToFormData($policy, [
            'roleDraft' => $roleDraft,
            'initialRole' => $initialRole,
            'availableLimitationTypes' => [],
        ]);
        $module = 'foo';
        $function = 'bar';
        $data->moduleFunction = "$module|$function";
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $newPolicyDraft = new PolicyDraft(['innerPolicy' => new Policy(['id' => 456])]);
        $updatedRoleDraft = new RoleDraft(['innerRole' => new Role(['policies' => [$existingPolicy, $newPolicyDraft]])]);
        $this->roleService
            ->expects($this->once())
            ->method('addPolicyByRoleDraft')
            ->with($roleDraft, $data)
            ->willReturn($updatedRoleDraft);

        $this->processor->processUpdatePolicy($event);
        self::assertSame($newPolicyDraft, $event->getData()->policyDraft);
    }

    public function testSavePolicy()
    {
        $policy = new PolicyDraft(['innerPolicy' => new Policy()]);
        $roleDraft = new RoleDraft();
        $initialRole = new Role();
        $data = (new PolicyMapper())->mapToFormData($policy, [
            'roleDraft' => $roleDraft,
            'initialRole' => $initialRole,
            'availableLimitationTypes' => [],
        ]);
        $module = 'foo';
        $function = 'bar';
        $data->moduleFunction = "$module|$function";
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $this->roleService
            ->expects($this->once())
            ->method('publishRoleDraft')
            ->with($roleDraft);

        $this->processor->processSavePolicy($event);
    }

    public function testRemoveDraftOnCreateData()
    {
        $policy = new PolicyDraft(['innerPolicy' => new Policy()]);
        $roleDraft = new RoleDraft();
        $initialRole = new Role();
        $data = (new PolicyMapper())->mapToFormData($policy, [
            'roleDraft' => $roleDraft,
            'initialRole' => $initialRole,
            'availableLimitationTypes' => [],
        ]);
        $module = 'foo';
        $function = 'bar';
        $data->moduleFunction = "$module|$function";
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $this->roleService
            ->expects($this->never())
            ->method('removePolicyByRoleDraft');

        $this->roleService
            ->expects($this->once())
            ->method('deleteRoleDraft')
            ->with($roleDraft);

        $this->processor->processRemoveDraft($event);
    }

    public function testRemoveDraftOnUpdateData()
    {
        $policy = new PolicyDraft(['innerPolicy' => new Policy(['id' => 123])]);
        $roleDraft = new RoleDraft();
        $initialRole = new Role();
        $data = (new PolicyMapper())->mapToFormData($policy, [
            'roleDraft' => $roleDraft,
            'initialRole' => $initialRole,
            'availableLimitationTypes' => [],
        ]);
        $module = 'foo';
        $function = 'bar';
        $data->moduleFunction = "$module|$function";
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $this->roleService
            ->expects($this->once())
            ->method('removePolicyByRoleDraft')
            ->with($roleDraft, $policy);

        $this->roleService
            ->expects($this->once())
            ->method('deleteRoleDraft')
            ->with($roleDraft);

        $this->processor->processRemoveDraft($event);
    }
}
