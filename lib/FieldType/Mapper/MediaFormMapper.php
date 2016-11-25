<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\Core\FieldType\Media\Type;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaFormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add('maxSize', IntegerType::class, [
                'required' => false,
                'property_path' => 'validatorConfiguration[FileSizeValidator][maxFileSize]',
                'label' => 'field_definition.ezmedia.max_file_size',
            ])
            ->add('mediaType', ChoiceType::class, [
                'choices' => [
                    'field_definition.ezmedia.type_html5_video' => Type::TYPE_HTML5_VIDEO,
                    'field_definition.ezmedia.type_flash' => Type::TYPE_FLASH,
                    'field_definition.ezmedia.type_quick_time' => Type::TYPE_QUICKTIME,
                    'field_definition.ezmedia.type_real_player' => Type::TYPE_REALPLAYER,
                    'field_definition.ezmedia.type_silverlight' => Type::TYPE_SILVERLIGHT,
                    'field_definition.ezmedia.type_windows_media_player' => Type::TYPE_WINDOWSMEDIA,
                    'field_definition.ezmedia.type_html5_audio' => Type::TYPE_HTML5_AUDIO,
                ],
                'choices_as_values' => true,
                'required' => true,
                'property_path' => 'fieldSettings[mediaType]',
                'label' => 'field_definition.ezmedia.media_type',
            ]);
    }

    /**
     * Fake method to set the translation domain for the extractor.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }
}
