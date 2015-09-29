<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Tests\Data\Mapper;

use eZ\Publish\Core\Repository\Values\User\Policy;
use eZ\Publish\Core\Repository\Values\User\RoleDraft;
use EzSystems\RepositoryForms\Data\Mapper\PolicyMapper;
use PHPUnit_Framework_TestCase;

class PolicyMapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testMapToCreateNoRoleDraft()
    {
        $policy = new Policy();
        (new PolicyMapper())->mapToFormData($policy);
    }

    public function testMapToCreate()
    {
        $policy = new Policy();
        $roleDraft = new RoleDraft();
        $data = (new PolicyMapper())->mapToFormData($policy, ['roleDraft' => $roleDraft]);
        self::assertInstanceOf('\EzSystems\RepositoryForms\Data\Role\PolicyCreateData', $data);
        self::assertSame($policy, $data->policy);
        self::assertSame($roleDraft, $data->roleDraft);
        self::assertTrue($data->isNew());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testMapToUpdateNoRoleDraft()
    {
        $policy = new Policy(['id' => 123]);
        (new PolicyMapper())->mapToFormData($policy);
    }

    public function testMapToUpdate()
    {
        $policy = new Policy(['id' => 123]);
        $roleDraft = new RoleDraft();
        $data = (new PolicyMapper())->mapToFormData($policy, ['roleDraft' => $roleDraft]);
        self::assertInstanceOf('\EzSystems\RepositoryForms\Data\Role\PolicyUpdateData', $data);
        self::assertSame($policy, $data->policy);
        self::assertSame($roleDraft, $data->roleDraft);
        self::assertFalse($data->isNew());
    }
}
