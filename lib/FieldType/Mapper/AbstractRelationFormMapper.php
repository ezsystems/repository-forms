<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Helper\TranslationHelper;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;

abstract class AbstractRelationFormMapper implements FieldDefinitionFormMapperInterface
{
    /**
     * @var ContentTypeService Used to fetch list of available content types
     */
    protected $contentTypeService;

    /**
     * @var TranslationHelper Translation helper, for translated content type names
     */
    protected $translationHelper;

    /**
     * @param ContentTypeService $contentTypeService
     * @param TranslationHelper $translationHelper
     */
    public function __construct(ContentTypeService $contentTypeService, TranslationHelper $translationHelper)
    {
        $this->contentTypeService = $contentTypeService;
        $this->translationHelper = $translationHelper;
    }

    /**
     * Fill a hash with all content types and their ids.
     * @return array
     */
    protected function getContentTypeHash()
    {
        $contentTypeHash = [];
        foreach ($this->contentTypeService->loadContentTypeGroups() as $contentTypeGroup) {
            foreach ($this->contentTypeService->loadContentTypes($contentTypeGroup) as $contentType) {
                $contentTypeHash[$this->translationHelper->getTranslatedByProperty($contentType, 'names')] = $contentType->identifier;
            }
        }
        ksort($contentTypeHash);

        return $contentTypeHash;
    }
}
