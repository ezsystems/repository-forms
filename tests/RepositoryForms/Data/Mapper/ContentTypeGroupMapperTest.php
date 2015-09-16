<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Tests\Data\Mapper;

use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\RepositoryForms\Data\Mapper\ContentTypeGroupMapper;
use PHPUnit_Framework_TestCase;

class ContentTypeGroupMapperTest extends PHPUnit_Framework_TestCase
{
    public function testMapToCreateData()
    {
        $contentTypeGroup = new ContentTypeGroup();
        $data = (new ContentTypeGroupMapper())->mapToFormData($contentTypeGroup);
        self::assertInstanceOf('\EzSystems\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupCreateData', $data);
        self::assertSame($contentTypeGroup, $data->contentTypeGroup);
    }

    public function testMapToUpdateData()
    {
        $id = 123;
        $contentTypeGroup = new ContentTypeGroup(['id' => $id, 'identifier' => 'Foo']);
        $data = (new ContentTypeGroupMapper())->mapToFormData($contentTypeGroup);
        self::assertInstanceOf('\EzSystems\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupUpdateData', $data);
        self::assertSame($contentTypeGroup, $data->contentTypeGroup);
        self::assertSame($id, $data->getId());
    }
}
