<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\FieldType\RelationList\Type;
use eZ\Publish\Core\Helper\TranslationHelper;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;

class RelationListFormMapper implements FieldDefinitionFormMapperInterface
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

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        // Fill a hash with all content types and their ids
        $contentTypeHash = [];
        foreach ($this->contentTypeService->loadContentTypeGroups() as $contentTypeGroup) {
            foreach ($this->contentTypeService->loadContentTypes($contentTypeGroup) as $contentType) {
                $contentTypeHash[$this->translationHelper->getTranslatedByProperty($contentType, 'names')] = $contentType->identifier;
            }
        }
        ksort($contentTypeHash);

        $fieldDefinitionForm
            ->add('selectionDefaultLocation', HiddenType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[selectionDefaultLocation]',
                'label' => 'field_definition.ezobjectrelationlist.selection_default_location',
            ])
            ->add('selectionContentTypes', ChoiceType::class, [
                'choices' => $contentTypeHash,
                'choices_as_values' => true,
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'property_path' => 'fieldSettings[selectionContentTypes]',
                'label' => 'field_definition.ezobjectrelationlist.selection_content_types',
            ]);
    }
}
