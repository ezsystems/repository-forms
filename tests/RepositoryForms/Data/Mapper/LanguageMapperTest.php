<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Data\Mapper;

use eZ\Publish\API\Repository\Values\Content\Language;
use EzSystems\RepositoryForms\Data\Mapper\LanguageMapper;
use EzSystems\RepositoryForms\Data\Language\LanguageCreateData;
use EzSystems\RepositoryForms\Data\Language\LanguageUpdateData;
use PHPUnit\Framework\TestCase;

class LanguageMapperTest extends TestCase
{
    public function testMapToLanguageCreateData()
    {
        $language = new Language();
        $languageData = (new LanguageMapper())->mapToFormData($language);
        self::assertInstanceOf(LanguageCreateData::class, $languageData);
        self::assertSame($language, $languageData->language);
    }

    public function testMapToLanguageUpdateData()
    {
        $languageId = 123;
        $language = new Language(['id' => $languageId, 'name' => 'Foo', 'languageCode' => 'foo']);
        $languageData = (new LanguageMapper())->mapToFormData($language);
        self::assertInstanceOf(LanguageUpdateData::class, $languageData);
        self::assertSame($language, $languageData->language);
        self::assertSame($languageId, $languageData->getId());
    }
}
