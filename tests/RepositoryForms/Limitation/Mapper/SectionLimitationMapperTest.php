<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\User\Limitation\SectionLimitation;
use EzSystems\RepositoryForms\Limitation\Mapper\SectionLimitationMapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SectionLimitationMapperTest extends TestCase
{
    /** @var SectionService|\PHPUnit_Framework_MockObject_MockObject */
    private $sectionServiceMock;

    /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var SectionLimitationMapper */
    private $mapper;

    protected function setUp()
    {
        $this->sectionServiceMock = $this->createMock(SectionService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->mapper = new SectionLimitationMapper($this->sectionServiceMock);
        $this->mapper->setLogger($this->logger);
    }

    public function testMapLimitationValue()
    {
        $values = ['3', '5', '7'];

        $expected = [];
        foreach ($values as $i => $value) {
            $expected[$i] = new Section([
                'id' => $value,
            ]);

            $this->sectionServiceMock
                ->expects($this->at($i))
                ->method('loadSection')
                ->with($value)
                ->willReturn($expected[$i]);
        }

        $result = $this->mapper->mapLimitationValue(new SectionLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEquals($expected, $result);
    }

    public function testMapLimitationValueWithNotExistingContentType()
    {
        $values = ['foo'];

        $this->sectionServiceMock
            ->expects($this->once())
            ->method('loadSection')
            ->with($values[0])
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Could not map limitation value: Section with id = foo not found');

        $actual = $this->mapper->mapLimitationValue(new SectionLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEmpty($actual);
    }
}
