<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Tests\Data\Mapper;

use eZ\Publish\API\Repository\Values\Content\Language;
use EzSystems\RepositoryForms\Data\Mapper\LanguageMapper;
use PHPUnit_Framework_TestCase;

class LanguageMapperTest extends PHPUnit_Framework_TestCase
{
    public function testMapToLanguageCreateData()
    {
        $language = new Language();
        $languageData = (new LanguageMapper())->mapToFormData($language);
        self::assertInstanceOf('\EzSystems\RepositoryForms\Data\Language\LanguageCreateData', $languageData);
        self::assertSame($language, $languageData->language);
    }

    public function testMapToLanguageUpdateData()
    {
        $languageId = 123;
        $language = new Language(['id' => $languageId, 'name' => 'Foo', 'languageCode' => 'foo']);
        $languageData = (new LanguageMapper())->mapToFormData($language);
        self::assertInstanceOf('\EzSystems\RepositoryForms\Data\Language\LanguageUpdateData', $languageData);
        self::assertSame($language, $languageData->language);
        self::assertSame($languageId, $languageData->getId());
    }
}
