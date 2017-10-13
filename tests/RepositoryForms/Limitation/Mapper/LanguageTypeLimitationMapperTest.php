<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\User\Limitation\LanguageLimitation;
use EzSystems\RepositoryForms\Limitation\Mapper\LanguageLimitationMapper;
use PHPUnit\Framework\TestCase;

class LanguageTypeLimitationMapperTest extends TestCase
{
    public function testMapLimitationValue()
    {
        $values = ['en_GB', 'en_US', 'pl_PL'];

        $languageServiceMock = $this->createMock(LanguageService::class);
        foreach ($values as $i => $value) {
            $languageServiceMock
                ->expects($this->at($i))
                ->method('loadLanguage')
                ->with($value)
                ->willReturnArgument(0);
        }

        $mapper = new LanguageLimitationMapper($languageServiceMock);
        $result = $mapper->mapLimitationValue(new LanguageLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEquals($values, $result);
    }
}
