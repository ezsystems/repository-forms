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

use eZ\Publish\Core\FieldType\Media\Type;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface;
use Symfony\Component\Form\FormInterface;

class MediaFormMapper implements FieldTypeFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add('maxSize', 'integer', [
                'required' => false,
                'property_path' => 'validatorConfiguration[FileSizeValidator][maxFileSize]',
                'label' => 'field_definition.ezmedia.max_file_size',
            ])
            ->add('mediaType', 'choice', [
                'choices' => [
                    Type::TYPE_HTML5_VIDEO => 'field_definition.ezmedia.type_html5_video',
                    Type::TYPE_FLASH => 'field_definition.ezmedia.type_flash',
                    Type::TYPE_QUICKTIME => 'field_definition.ezmedia.type_quick_time',
                    Type::TYPE_REALPLAYER => 'field_definition.ezmedia.type_real_player',
                    Type::TYPE_SILVERLIGHT => 'field_definition.ezmedia.type_silverlight',
                    Type::TYPE_WINDOWSMEDIA => 'field_definition.ezmedia.type_windows_media_player',
                    Type::TYPE_HTML5_AUDIO => 'field_definition.ezmedia.type_html5_audio',
                ],
                'required' => true,
                'property_path' => 'fieldSettings[mediaType]',
                'label' => 'field_definition.ezmedia.media_type',
            ]);
    }

    /**
     * "Maps" Field form to current FieldType.
     * Allows to add form fields for content edition.
     *
     * @param FormInterface $fieldForm Form for the current Field.
     * @param FieldData $data Underlying data for current Field form.
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        // TODO: Implement mapFieldForm() method.
    }
}
