<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\FieldType\Date\Value;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * DataTransformer for Date\Value.
 */
class DateValueTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return int|null
     *
     * @throws TransformationFailedException
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Value) {
            throw new TransformationFailedException(
                sprintf('Expected a %s, got %s instead', Value::class, gettype($value))
            );
        }

        if (null === $value->date) {
            return null;
        }

        return $value->date->getTimestamp() + $value->date->getOffset();
    }

    /**
     * @param int|mixed $value
     *
     * @return Value|null
     *
     * @throws InvalidArgumentException
     * @throws TransformationFailedException
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        if (!is_numeric($value)) {
            throw new TransformationFailedException(
                sprintf('Expected a numeric, got %s instead', gettype($value))
            );
        }

        return Value::fromTimestamp($value);
    }
}
