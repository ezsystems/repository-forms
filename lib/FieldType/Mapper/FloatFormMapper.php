<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\DataTransformer\FieldValueTransformer;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FloatFormMapper implements FieldDefinitionFormMapperInterface
{
    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $fieldDefinition)
    {
        $defaultValueForm = $fieldDefinitionForm
            ->getConfig()
            ->getFormFactory()
            ->createBuilder()
            ->create('defaultValue', NumberType::class, [
                'required' => false,
                'label' => 'field_definition.ezfloat.default_value',
            ])
            ->addModelTransformer(new FieldValueTransformer($this->fieldTypeService->getFieldType($fieldDefinition->getFieldTypeIdentifier())))
            ->setAutoInitialize(false)
            ->getForm();

        $fieldDefinitionForm
            ->add(
                'minValue', NumberType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[FloatValueValidator][minFloatValue]',
                    'label' => 'field_definition.ezfloat.min_value',
                ]
            )
            ->add(
                'maxValue', NumberType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[FloatValueValidator][maxFloatValue]',
                    'label' => 'field_definition.ezfloat.max_value',
                ]
            )
            ->add($defaultValueForm);
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
