<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\DataTransformer;

use eZ\Publish\Core\FieldType\Country\Value;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * DataTransformer for Country\Value to be used with form type handling only single selection.
 * Needed to display the form field correctly and transform it back to an appropriate value object.
 */
class SingleCountryValueTransformer implements DataTransformerInterface
{
    /**
     * @var array Array of countries from ezpublish.fieldType.ezcountry.data
     */
    protected $countriesInfo;

    public function __construct(array $countriesInfo)
    {
        $this->countriesInfo = $countriesInfo;
    }

    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        if (empty($value->countries)) {
            return null;
        }

        $country = current($value->countries);
        if (!isset($country['Alpha2'])) {
            throw new TransformationFailedException('Missing Alpha2 key');
        }

        return $country['Alpha2'];
    }

    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        return new Value([$value => $this->countriesInfo[$value]]);
    }
}
