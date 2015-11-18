<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\FieldType\DataTransformer\FieldValueTransformer;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface;
use Symfony\Component\Form\FormInterface;

abstract class AbstractMapper implements FieldTypeFormMapperInterface
{
    /**
     * @var FieldTypeService
     */
    private $fieldTypeService;

    /**
     * @param FieldTypeService $fieldTypeService
     */
    public function setFieldTypeService(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * "Maps" Field form to current FieldType.
     * Allows to add form fields for content edition.
     *
     * To map the field value, FieldValueTransformer will be used.
     *
     * @param FormInterface $fieldForm Form for the current Field.
     * @param FieldData $data Underlying data for current Field form.
     */
    public function mapFieldForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $label = $fieldDefinition->getName($formConfig->getOption('languageCode')) ?: reset($fieldDefinition->getNames());

        $fieldForm
            ->add(
                // Creating from FormBuilder as we need to add a DataTransformer.
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        $this->getContentFormFieldType(),
                        $this->getContentFormFieldTypeOptions($fieldForm, $data) + [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $label,
                        ]
                    )
                    ->addModelTransformer(new FieldValueTransformer($this->getFieldType($fieldDefinition)))
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)->getForm()
            );
    }

    /**
     * @param FieldDefinition $fieldDefinition
     *
     * @return \eZ\Publish\API\Repository\FieldType
     */
    protected function getFieldType(FieldDefinition $fieldDefinition)
    {
        return $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);
    }

    /**
     * Returns the form FieldType to be used for content editing.
     *
     * @return string|\Symfony\Component\Form\FormTypeInterface
     */
    abstract protected function getContentFormFieldType();

    /**
     * Returns options to be passed to the form FieldType used in content edition context.
     *
     * @param FormInterface $fieldForm
     * @param FieldData $data
     *
     * @return array
     */
    protected function getContentFormFieldTypeOptions(FormInterface $fieldForm, FieldData $data)
    {
        return [];
    }
}
