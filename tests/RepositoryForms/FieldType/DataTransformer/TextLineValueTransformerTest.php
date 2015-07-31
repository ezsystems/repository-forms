<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Tests\FieldType\DataTransformer;

use eZ\Publish\Core\FieldType\TextLine\Value;
use EzSystems\RepositoryForms\FieldType\DataTransformer\TextLineValueTransformer;
use PHPUnit_Framework_TestCase;

class TextLineValueTransformerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider transformProvider
     */
    public function testTransform($valueAsString)
    {
        $transformer = new TextLineValueTransformer();
        $value = new Value($valueAsString);
        self::assertSame($valueAsString, $transformer->transform($value));
    }

    public function transformProvider()
    {
        return [
            ['foo'],
            ['bar'],
            ['bar biz boz'],
            ["Les chaussettes de l'archiduchesse sont-elles sèches?"],
        ];
    }

    /**
     * @dataProvider transformNullProvider
     */
    public function testTransformNull($value)
    {
        $transformer = new TextLineValueTransformer();
        self::assertNull($transformer->transform($value));
    }

    public function transformNullProvider()
    {
        return [
            [new \eZ\Publish\Core\FieldType\DateAndTime\Value()],
            [123],
            [false],
            [['foo']],
        ];
    }

    public function testReverseTransformNull()
    {
        $transformer = new TextLineValueTransformer();
        self::assertNull($transformer->reverseTransform(''));
    }

    /**
     * @dataProvider transformProvider
     */
    public function testReverseTransform($valueAsString)
    {
        $transformer = new TextLineValueTransformer();
        $expectedValue = new Value($valueAsString);
        self::assertEquals($expectedValue, $transformer->reverseTransform($valueAsString));
    }
}
