<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\FieldType\User\Value as ApiUserValue;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\User\UserAccountFieldData;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\RepositoryForms\Form\Type\User\UserAccountType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Maps a user FieldType.
 */
final class UserAccountFieldValueFormMapper implements FieldValueFormMapperInterface
{
    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * Maps Field form to current FieldType based on the configured form type (self::$formType).
     *
     * @param FormInterface $fieldForm Form for the current Field.
     * @param FieldData $data Underlying data for current Field form.
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($formConfig->getOption('languageCode')) ?: reset($names);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create('value', UserAccountType::class, ['required' => $fieldDefinition->isRequired, 'label' => $label])
                    ->addModelTransformer(
                        new CallbackTransformer(
                            function (ApiUserValue $data) {
                                return new UserAccountFieldData($data->login, null, $data->email);
                            },
                            function ($submittedData) {
                                return new UserAccountFieldData(
                                    $submittedData->username,
                                    $submittedData->password,
                                    $submittedData->email
                                );
                            }
                        )
                    )
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
