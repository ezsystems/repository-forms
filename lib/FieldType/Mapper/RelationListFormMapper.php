<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\FieldType\RelationList\Type;
use eZ\Publish\Core\Helper\TranslationHelper;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface;
use Symfony\Component\Form\FormInterface;

class RelationListFormMapper implements FieldTypeFormMapperInterface
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
                $contentTypeHash[$contentType->identifier] = $this->translationHelper->getTranslatedByProperty($contentType, 'names');
            }
        }
        sort($contentTypeHash);

        $fieldDefinitionForm
            ->add('selectionDefaultLocation', 'hidden', [
                'required' => false,
                'property_path' => 'fieldSettings[selectionDefaultLocation]',
                'label' => 'field_definition.ezobjectrelationlist.selection_default_location',
            ])
            ->add('selectionContentTypes', 'choice', [
                'choices' => $contentTypeHash,
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'property_path' => 'fieldSettings[selectionContentTypes]',
                'label' => 'field_definition.ezobjectrelationlist.selection_content_types',
            ]);
    }

    /**
     * "Maps" Field form to current FieldType.
     * Allows to add form fields for content edition.
     *
     * @param FormInterface $fieldForm Form for the current Field.
     * @param FieldData $data Underlying data for current Field form.
     */
    public function mapFieldForm(FormInterface $fieldForm, FieldData $data)
    {
        // TODO: Implement mapFieldForm() method.
    }
}
