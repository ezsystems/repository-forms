<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\Tests\FieldType\DataTransformer;

use eZ\Publish\Core\FieldType\Country\Value;
use EzSystems\RepositoryForms\FieldType\DataTransformer\SingleCountryValueTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class SingleCountryValueTransformerTest extends TestCase
{
    private const COUNTRIES_INFO = [
        'AF' => ['Name' => 'Afghanistan', 'Alpha2' => 'AF', 'Alpha3' => 'AFG', 'IDC' => '93'],
        'AX' => ['Name' => 'Åland', 'Alpha2' => 'AX', 'Alpha3' => 'ALA', 'IDC' => '358'],
        'AQ' => ['Name' => 'Antarctica', 'Alpha2' => 'AQ', 'Alpha3' => 'ATA', 'IDC' => '672'],
        'AG' => ['Name' => 'Antigua and Barbuda', 'Alpha2' => 'AG', 'Alpha3' => 'ATG', 'IDC' => '1268'],
        'AM' => ['Name' => 'Armenia', 'Alpha2' => 'AM', 'Alpha3' => 'ARM', 'IDC' => '374'],
        'BB' => ['Name' => 'Barbados', 'Alpha2' => 'BB', 'Alpha3' => 'BRB', 'IDC' => '1246'],
        'BJ' => ['Name' => 'Benin', 'Alpha2' => 'BJ', 'Alpha3' => 'BEN', 'IDC' => '229'],
        'BM' => ['Name' => 'Bermuda', 'Alpha2' => 'BM', 'Alpha3' => 'BMU', 'IDC' => '1441'],
        'BT' => ['Name' => 'Bhutan', 'Alpha2' => 'BT', 'Alpha3' => 'BTN', 'IDC' => '975'],
        'BA' => ['Name' => 'Bosnia and Herzegovina', 'Alpha2' => 'BA', 'Alpha3' => 'BIH', 'IDC' => '387'],
        'BW' => ['Name' => 'Botswana', 'Alpha2' => 'BW', 'Alpha3' => 'BWA', 'IDC' => '267'],
        'BV' => ['Name' => 'Bouvet Island', 'Alpha2' => 'BV', 'Alpha3' => 'BVT', 'IDC' => '47'],
        'IO' => ['Name' => 'British Indian Ocean Territory', 'Alpha2' => 'IO', 'Alpha3' => 'IOT', 'IDC' => '246'],
        'BN' => ['Name' => 'Brunei Darussalam', 'Alpha2' => 'BN', 'Alpha3' => 'BRN', 'IDC' => '673'],
        'BF' => ['Name' => 'Burkina Faso', 'Alpha2' => 'BF', 'Alpha3' => 'BFA', 'IDC' => '226'],
        'KH' => ['Name' => 'Cambodia', 'Alpha2' => 'KH', 'Alpha3' => 'KHM', 'IDC' => '855'],
        'CV' => ['Name' => 'Cape Verde', 'Alpha2' => 'CV', 'Alpha3' => 'CPV', 'IDC' => '238'],
        'CF' => ['Name' => 'Central African Republic', 'Alpha2' => 'CF', 'Alpha3' => 'CAF', 'IDC' => '236'],
        'CN' => ['Name' => 'China', 'Alpha2' => 'CN', 'Alpha3' => 'CHN', 'IDC' => '86'],
        'CC' => ['Name' => 'Cocos (Keeling) Islands', 'Alpha2' => 'CC', 'Alpha3' => 'CCK', 'IDC' => '61'],
        'BL' => ['Name' => 'Saint Barthélemy', 'Alpha2' => 'BL', 'Alpha3' => 'BLM', 'IDC' => '590'],
        'GS' => ['Name' => 'South Georgia and The South Sandwich Islands', 'Alpha2' => 'GS', 'Alpha3' => 'SGS', 'IDC' => '500'],
        'TW' => ['Name' => 'Taiwan', 'Alpha2' => 'TW', 'Alpha3' => 'TWN', 'IDC' => '886'],
        'ZW' => ['Name' => 'Zimbabwe', 'Alpha2' => 'ZW', 'Alpha3' => 'ZWE', 'IDC' => '263'],
    ];

    /** @var \EzSystems\RepositoryForms\FieldType\DataTransformer\SingleCountryValueTransformer */
    private $transformer;

    protected function setUp(): void
    {
        $this->transformer = new SingleCountryValueTransformer(self::COUNTRIES_INFO);
    }

    /**
     * @dataProvider dataProviderForTransform
     */
    public function testTransform(?Value $value, ?string $expectedValue): void
    {
        $this->assertEquals($expectedValue, $this->transformer->transform($value));
    }

    public function dataProviderForTransform(): iterable
    {
        yield 'country' => [
            new Value(['ZW' => self::COUNTRIES_INFO['ZW']]),
            'ZW',
        ];

        yield 'empty_array' => [
            new Value([]),
            null,
        ];

        yield 'null' => [null, null];
    }

    public function testTransformThrowsTransformationFailedException(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Missing Alpha2 key');

        $this->transformer->transform(new Value([
            'AF' => ['Name' => 'Afghanistan', 'Alpha3' => 'AFG', 'IDC' => '93'],
        ]));
    }

    /**
     * @dataProvider dataProviderForReverseTransform
     */
    public function testReverseTransform($value, ?Value $expectedValue): void
    {
        $this->assertEquals($expectedValue, $this->transformer->reverseTransform($value));
    }

    public function dataProviderForReverseTransform(): iterable
    {
        yield 'country' => [
            'ZW',
            new Value(['ZW' => self::COUNTRIES_INFO['ZW']]),
        ];

        yield 'null' => [null, null];
    }
}
