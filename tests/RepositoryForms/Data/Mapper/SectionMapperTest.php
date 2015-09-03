<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Tests\Data\Mapper;

use eZ\Publish\API\Repository\Values\Content\Section;
use EzSystems\RepositoryForms\Data\Mapper\SectionMapper;
use PHPUnit_Framework_TestCase;

class SectionMapperTest extends PHPUnit_Framework_TestCase
{
    public function testMapToSectionCreateData()
    {
        $section = new Section();
        $sectionData = (new SectionMapper())->mapToFormData($section);
        self::assertInstanceOf('\EzSystems\RepositoryForms\Data\Section\SectionCreateData', $sectionData);
        self::assertSame($section, $sectionData->section);
    }

    public function testMapToSectionUpdateData()
    {
        $sectionId = 123;
        $section = new Section(['id' => $sectionId, 'name' => 'Foo', 'identifier' => 'foo']);
        $sectionData = (new SectionMapper())->mapToFormData($section);
        self::assertInstanceOf('\EzSystems\RepositoryForms\Data\Section\SectionUpdateData', $sectionData);
        self::assertSame($section, $sectionData->section);
        self::assertSame($sectionId, $sectionData->getId());
    }
}
