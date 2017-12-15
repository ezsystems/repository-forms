<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\DataTransformer;

/**
 * DataTransformer for Country\Value.
 * Needed to display the form field correctly and transform it back to an appropriate value object.
 *
 * @deprecated Since 1.10. will be removed in 2.0. Please use the MultipleCountryValueTransformer instead
 */
class CountryValueTransformer extends MultipleCountryValueTransformer
{
}
