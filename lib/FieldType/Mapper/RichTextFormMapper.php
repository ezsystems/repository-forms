<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\FieldType\RichText\Converter;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\FieldType\DataTransformer\RichTextValueTransformer;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RichTextFormMapper implements FieldValueFormMapperInterface
{
    /**
     * @var FieldTypeService
     */
    private $fieldTypeService;

    /**
     * @var \eZ\Publish\Core\FieldType\RichText\Converter
     */
    private $docbookToXhtml5EditConverter;

    public function __construct(FieldTypeService $fieldTypeService, Converter $docbookToXhtml5EditConverter)
    {
        $this->fieldTypeService = $fieldTypeService;
        $this->docbookToXhtml5EditConverter = $docbookToXhtml5EditConverter;
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create('value', TextareaType::class, [
                        'required' => $fieldDefinition->isRequired,
                        'label' => $fieldDefinition->getName($formConfig->getOption('languageCode')),
                    ])
                    ->addModelTransformer(new RichTextValueTransformer($this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier), $this->docbookToXhtml5EditConverter))
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
