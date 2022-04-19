<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * A FormDataMapper will convert a value object from eZ content repository to a usable form data.
 */
interface FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @return mixed
     */
    public function mapToFormData(ValueObject $repositoryValueObject, array $params = []);
}
