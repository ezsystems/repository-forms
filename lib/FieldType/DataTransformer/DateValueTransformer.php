<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\DataTransformer;

use DateTime;
use eZ\Publish\Core\FieldType\Date\Value;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * DataTransformer for Date\Value.
 */
class DateValueTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return DateTime|null
     */
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        return $value->date;
    }

    /**
     * @param mixed $value
     *
     * @return Value|null
     */
    public function reverseTransform($value)
    {
        if ($value === null || !$value instanceof DateTime) {
            return null;
        }

        return new Value($value);
    }
}
