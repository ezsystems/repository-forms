<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Data\Mapper;

use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\RepositoryForms\Data\Mapper\ContentTypeGroupMapper;
use EzSystems\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupCreateData;
use EzSystems\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupUpdateData;
use PHPUnit\Framework\TestCase;

class ContentTypeGroupMapperTest extends TestCase
{
    public function testMapToCreateData()
    {
        $contentTypeGroup = new ContentTypeGroup();
        $data = (new ContentTypeGroupMapper())->mapToFormData($contentTypeGroup);
        self::assertInstanceOf(ContentTypeGroupCreateData::class, $data);
        self::assertSame($contentTypeGroup, $data->contentTypeGroup);
    }

    public function testMapToUpdateData()
    {
        $id = 123;
        $contentTypeGroup = new ContentTypeGroup(['id' => $id, 'identifier' => 'Foo']);
        $data = (new ContentTypeGroupMapper())->mapToFormData($contentTypeGroup);
        self::assertInstanceOf(ContentTypeGroupUpdateData::class, $data);
        self::assertSame($contentTypeGroup, $data->contentTypeGroup);
        self::assertSame($id, $data->getId());
    }
}
