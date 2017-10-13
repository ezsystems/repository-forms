<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use EzSystems\RepositoryForms\Limitation\Mapper\ContentTypeLimitationMapper;
use PHPUnit\Framework\TestCase;

class ContentTypeLimitationMapperTest extends TestCase
{
    public function testMapLimitationValue()
    {
        $values = ['foo', 'bar', 'baz'];

        $contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        foreach ($values as $i => $value) {
            $contentTypeServiceMock
                ->expects($this->at($i))
                ->method('loadContentType')
                ->with($value)
                ->willReturn($value);
        }

        $mapper = new ContentTypeLimitationMapper($contentTypeServiceMock);
        $result = $mapper->mapLimitationValue(new ContentTypeLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEquals($values, $result);
        $this->assertCount(3, $result);
    }
}
