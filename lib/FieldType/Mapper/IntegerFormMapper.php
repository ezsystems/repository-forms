<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\DataTransformer\FieldValueTransformer;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezinteger FieldType.
 */
class IntegerFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    /** @var FieldTypeService */
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
            ->create('defaultValue', IntegerType::class, [
                'required' => false,
                'label' => 'field_definition.ezinteger.default_value',
            ])
            ->addModelTransformer(new FieldValueTransformer($this->fieldTypeService->getFieldType($fieldDefinition->getFieldTypeIdentifier())))
            ->setAutoInitialize(false)
            ->getForm();

        $fieldDefinitionForm
            ->add(
                'minValue', IntegerType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[IntegerValueValidator][minIntegerValue]',
                    'label' => 'field_definition.ezinteger.min_value',
                ]
            )
            ->add(
                'maxValue', IntegerType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[IntegerValueValidator][maxIntegerValue]',
                    'label' => 'field_definition.ezinteger.max_value',
                ]
            )
            ->add($defaultValueForm);
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        IntegerType::class,
                        [
                            'block_name' => $fieldDefinition->fieldTypeIdentifier,
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName($formConfig->getOption('languageCode')),
                            'attr' => $this->getAttributes($fieldDefinition),
                        ]
                    )
                    ->addModelTransformer(
                        new FieldValueTransformer(
                            $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier)
                        )
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }

    private function getAttributes(FieldDefinition $fieldDefinition)
    {
        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        $attributes = ['step' => 1];

        if (null !== $validatorConfiguration['IntegerValueValidator']['minIntegerValue']) {
            $attributes['min'] = $validatorConfiguration['IntegerValueValidator']['minIntegerValue'];
        }

        if (null !== $validatorConfiguration['IntegerValueValidator']['maxIntegerValue']) {
            $attributes['max'] = $validatorConfiguration['IntegerValueValidator']['maxIntegerValue'];
        }

        return $attributes;
    }
}
