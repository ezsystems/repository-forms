<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use eZ\Publish\API\Repository\Values\User\Limitation\ObjectStateLimitation;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectState;
use EzSystems\RepositoryForms\Limitation\Mapper\ObjectStateLimitationMapper;
use PHPUnit\Framework\TestCase;

class ObjectStateLimitationMapperTest extends TestCase
{
    public function testMapLimitationValue()
    {
        $values = ['foo', 'bar', 'baz'];

        $objectStateServiceMock = $this->createMock(ObjectStateService::class);
        foreach ($values as $i => $value) {
            $stateMock = $this->createStateMock($value);

            $objectStateServiceMock
                ->expects($this->at($i))
                ->method('loadObjectState')
                ->with($value)
                ->willReturn($stateMock);
        }

        $mapper = new ObjectStateLimitationMapper($objectStateServiceMock);
        $result = $mapper->mapLimitationValue(new ObjectStateLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEquals([
            'foo:foo', 'bar:bar', 'baz:baz',
        ], $result);
    }

    private function createStateMock($value)
    {
        $stateGroupMock = $this->createMock(ObjectStateGroup::class);
        $stateGroupMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn($value);

        $stateMock = $this->createMock(ObjectState::class);
        $stateMock
            ->expects($this->any())
            ->method('getObjectStateGroup')
            ->willReturn($stateGroupMock);

        $stateMock
            ->expects($this->any())
            ->method('getName')
            ->willReturn($value);

        return $stateMock;
    }
}
