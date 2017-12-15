<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Data\Mapper;

use eZ\Publish\Core\Repository\Values\User\Policy;
use eZ\Publish\Core\Repository\Values\User\PolicyDraft;
use eZ\Publish\Core\Repository\Values\User\Role;
use eZ\Publish\Core\Repository\Values\User\RoleDraft;
use EzSystems\RepositoryForms\Data\Mapper\PolicyMapper;
use EzSystems\RepositoryForms\Data\Role\PolicyCreateData;
use EzSystems\RepositoryForms\Data\Role\PolicyUpdateData;
use PHPUnit\Framework\TestCase;

class PolicyMapperTest extends TestCase
{
    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testMapToCreateNoRoleDraft()
    {
        $policy = new PolicyDraft();
        (new PolicyMapper())->mapToFormData($policy);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testMapToCreateNoInitialRole()
    {
        $policy = new PolicyDraft();
        (new PolicyMapper())->mapToFormData($policy, ['roleDraft' => new RoleDraft(), 'availableLimitationTypes' => []]);
    }

    public function testMapToCreate()
    {
        $policy = new PolicyDraft(['innerPolicy' => new Policy()]);
        $roleDraft = new RoleDraft();
        $initialRole = new Role();
        $data = (new PolicyMapper())->mapToFormData($policy, [
            'roleDraft' => $roleDraft,
            'initialRole' => $initialRole,
            'availableLimitationTypes' => [],
        ]);
        self::assertInstanceOf(PolicyCreateData::class, $data);
        self::assertSame($policy, $data->policyDraft);
        self::assertSame($roleDraft, $data->roleDraft);
        self::assertSame($initialRole, $data->initialRole);
        self::assertTrue($data->isNew());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testMapToUpdateNoRoleDraft()
    {
        $policy = new PolicyDraft(['innerPolicy' => new Policy(['id' => 123])]);
        (new PolicyMapper())->mapToFormData($policy);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testMapToUpdateNoInitialRole()
    {
        $policy = new PolicyDraft(['innerPolicy' => new Policy(['id' => 123])]);
        $roleDraft = new RoleDraft();
        (new PolicyMapper())->mapToFormData($policy, ['roleDraft' => $roleDraft, 'availableLimitationTypes' => []]);
    }

    public function testMapToUpdate()
    {
        $policy = new PolicyDraft([
            'originalId' => 123,
            'innerPolicy' => new Policy([
                'id' => 456,
                'module' => 'foo',
                'function' => 'bar',
            ]),
        ]);
        $roleDraft = new RoleDraft();
        $initialRole = new Role();
        $data = (new PolicyMapper())->mapToFormData($policy, [
            'roleDraft' => $roleDraft,
            'initialRole' => $initialRole,
            'availableLimitationTypes' => [],
        ]);
        self::assertInstanceOf(PolicyUpdateData::class, $data);
        self::assertSame($policy, $data->policyDraft);
        self::assertSame($roleDraft, $data->roleDraft);
        self::assertSame($initialRole, $data->initialRole);
        self::assertSame('foo|bar', $data->moduleFunction);
        self::assertFalse($data->isNew());
    }
}
