<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Data transformer from PHP DateInterval to array for form inputs.
 */
class SelectionOptionDefaultValueTransformer implements DataTransformerInterface
{
    /**
     * @var options
     */
    private $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function transform($defaultValue)
    {
        if (!is_array($defaultValue)) {
            return [];
        }

        return $defaultValue;
    }

    public function reverseTransform($defaults)
    {
        if (!is_array($defaults)) {
            return [];
        }

        $defaultValue = [];

        foreach ($defaults as $key => $value) {
            if ($value) {
                $defaultValue[] = $key;
            }
        }

        return $defaultValue;
    }
}
