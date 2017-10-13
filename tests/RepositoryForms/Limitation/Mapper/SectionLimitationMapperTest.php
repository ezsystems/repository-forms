<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\User\Limitation\SectionLimitation;
use EzSystems\RepositoryForms\Limitation\Mapper\SectionLimitationMapper;
use PHPUnit\Framework\TestCase;

class SectionLimitationMapperTest extends TestCase
{
    public function testMapLimitationValue()
    {
        $values = ['3', '5', '7'];

        $sectionServiceMock = $this->createMock(SectionService::class);

        $expected = [];
        foreach ($values as $i => $value) {
            $expected[$i] = new Section([
                'id' => $value,
            ]);

            $sectionServiceMock
                ->expects($this->at($i))
                ->method('loadSection')
                ->with($value)
                ->willReturn($expected[$i]);
        }

        $mapper = new SectionLimitationMapper($sectionServiceMock);
        $result = $mapper->mapLimitationValue(new SectionLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEquals($expected, $result);
    }
}
