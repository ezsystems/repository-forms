<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\FieldType\DataTransformer\FieldValueTransformer;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;

class CheckboxFormMapper implements FieldValueFormMapperInterface
{
    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $label = $fieldDefinition->getName($formConfig->getOption('languageCode')) ?: reset($fieldDefinition->getNames());

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        CheckboxType::class,
                        ['required' => $fieldDefinition->isRequired, 'label' => $label]
                    )
                    ->addModelTransformer(new FieldValueTransformer($this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier)))
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }
}
