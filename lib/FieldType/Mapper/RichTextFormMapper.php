<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\Core\FieldType\RichText\Converter;
use eZ\Publish\Core\FieldType\RichText\Value as RichTextValue;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RichTextFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    /*
     * @var eZ\Publish\Core\FieldType\RichText\Converter
     */
    private $xhtml5editConverter;

    public function __construct($fieldTypeService, Converter $xhtml5editConverter)
    {
        $this->fieldTypeService = $fieldTypeService;
        $this->xhtml5editConverter = $xhtml5editConverter;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $xhtml5editConverter = $this->xhtml5editConverter;
        $fieldDefinition = $data->fieldDefinition;
        $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);
        $formConfig = $fieldForm->getConfig();
        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($formConfig->getOption('languageCode')) ?: reset($names);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        TextareaType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'attr' => [
                                'cols' => 30,
                                'rows' => 10,
                            ],
                            'label' => $label,
                        ]
                    )
                    ->addModelTransformer(
                        new CallbackTransformer(
                            function (RichTextValue $value) use ($xhtml5editConverter) {
                                return $xhtml5editConverter->convert($value->xml)->saveXML();
                            },
                            function ($submittedData) use ($fieldType) {
                                return $fieldType->fromHash(['xml' => html_entity_decode($submittedData)]);
                            }
                        )
                    )
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)
                    ->getForm()
            );
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
