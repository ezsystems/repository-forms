<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Data\Content\ContentCreateData;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form data mapper for content create without a draft.
 */
class ContentCreateMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param ValueObject|\eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @param array $params
     *
     * @return ContentCreateData
     */
    public function mapToFormData(ValueObject $contentType, array $params = [])
    {
        $resolver = (new OptionsResolver())->setRequired('mainLanguageCode');
        $params = $resolver->resolve($params);

        $data = new ContentCreateData(['contentType' => $contentType, 'mainLanguageCode' => $params['mainLanguageCode']]);
        foreach ($contentType->fieldDefinitions as $fieldDef) {
            $data->addFieldData(new FieldData(['fieldDefinition' => $fieldDef]));
        }

        return $data;
    }
}
