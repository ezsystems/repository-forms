<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Limitation\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * DataTransformer for UDWBasedMapper.
 * Needed to display the form field correctly and transform it back to an appropriate value object.
 */
class UDWBasedValueTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!is_array($value)) {
            return null;
        }

        $locations = [];
        foreach ($value as $key => $path) {
            $locations[] = $this->extractLocationIdFromPath($path);
        }

        return implode(',', $locations);
    }

    public function reverseTransform($value)
    {
        if (!is_string($value)) {
            return null;
        }

        return explode(',', $value);
    }

    /**
     * Extracts and returns an item id from a path, e.g. /1/2/58 => 58.
     *
     * @param string $path
     *
     * @return mixed
     */
    private function extractLocationIdFromPath($path)
    {
        $pathParts = explode('/', trim($path, '/'));

        return array_pop($pathParts);
    }
}
