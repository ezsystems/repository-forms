<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use EzSystems\RepositoryForms\Limitation\Mapper\ContentTypeLimitationMapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ContentTypeLimitationMapperTest extends TestCase
{
    /** @var ContentTypeService|\PHPUnit_Framework_MockObject_MockObject */
    private $contentTypeService;

    /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var ContentTypeLimitationMapper */
    private $mapper;

    public function setUp()
    {
        $this->contentTypeService = $this->createMock(ContentTypeService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->mapper = new ContentTypeLimitationMapper($this->contentTypeService);
        $this->mapper->setLogger($this->logger);
    }

    public function testMapLimitationValue()
    {
        $values = ['foo', 'bar', 'baz'];

        foreach ($values as $i => $value) {
            $this->contentTypeService
                ->expects($this->at($i))
                ->method('loadContentType')
                ->with($value)
                ->willReturn($value);
        }

        $result = $this->mapper->mapLimitationValue(new ContentTypeLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEquals($values, $result);
        $this->assertCount(3, $result);
    }

    public function testMapLimitationValueWithNotExistingContentType()
    {
        $values = ['foo'];

        $this->contentTypeService
            ->expects($this->once())
            ->method('loadContentType')
            ->with($values[0])
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Could not map limitation value: Content Type with id = foo not found');

        $actual = $this->mapper->mapLimitationValue(new ContentTypeLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEmpty($actual);
    }
}
