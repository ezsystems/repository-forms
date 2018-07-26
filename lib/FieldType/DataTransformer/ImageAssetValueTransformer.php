<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\FieldType\DataTransformer;

use eZ\Publish\Core\FieldType\ImageAsset\Value;
use Symfony\Component\Form\DataTransformerInterface;

class ImageAssetValueTransformer extends AbstractBinaryBaseTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        return array_merge(
            $this->getDefaultProperties(),
            ['destinationContentId' => $value->destinationContentId]
        );
    }

    public function reverseTransform($value)
    {
        if ($value === null || !is_array($value)) {
            return null;
        }

        return new Value($value['destinationContentId']);
    }
}
