<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\Core\FieldType\User\Value as ApiUserValue;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\Data\User\UserAccountFieldData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\RepositoryForms\Form\Type\FieldDefinition\User\PasswordConstraintCheckboxType;
use EzSystems\RepositoryForms\Form\Type\FieldType\UserAccountFieldType;
use EzSystems\RepositoryForms\Validator\Constraints\UserAccountPassword;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Maps a user FieldType.
 */
final class UserAccountFieldValueFormMapper implements FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface
{
    /**
     * Maps Field form to current FieldType based on the configured form type (self::$formType).
     *
     * @param FormInterface $fieldForm Form for the current Field.
     * @param FieldData $data Underlying data for current Field form.
     *
     * @throws AlreadySubmittedException
     * @throws LogicException
     * @throws UnexpectedTypeException
     * @throws InvalidOptionsException
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $rootForm = $fieldForm->getRoot()->getRoot();
        $formIntent = $rootForm->getConfig()->getOption('intent');

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create('value', UserAccountFieldType::class, [
                        'required' => true,
                        'label' => $fieldDefinition->getName(),
                        'intent' => $formIntent,
                        'constraints' => [
                            new UserAccountPassword(['contentType' => $rootForm->getData()->contentType]),
                        ],
                    ])
                    ->addModelTransformer($this->getModelTransformer())
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    /**
     * {@inheritdoc}
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $propertyPathPrefix = 'validatorConfiguration[PasswordValueValidator]';

        $fieldDefinitionForm->add('requireAtLeastOneUpperCaseCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $propertyPathPrefix . '[requireAtLeastOneUpperCaseCharacter]',
        ]);

        $fieldDefinitionForm->add('requireAtLeastOneLowerCaseCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $propertyPathPrefix . '[requireAtLeastOneLowerCaseCharacter]',
        ]);

        $fieldDefinitionForm->add('requireAtLeastOneNumericCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $propertyPathPrefix . '[requireAtLeastOneNumericCharacter]',
        ]);

        $fieldDefinitionForm->add('requireAtLeastOneNonAlphanumericCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $propertyPathPrefix . '[requireAtLeastOneNonAlphanumericCharacter]',
        ]);

        $fieldDefinitionForm->add('minLength', IntegerType::class, [
            'required' => false,
            'property_path' => $propertyPathPrefix . '[minLength]',
            'label' => 'field_definition.ezuser.min_length',
            'constraints' => [
                new Range(['min' => 0, 'max' => 255]),
            ],
        ]);
    }

    /**
     * Fake method to set the translation domain for the extractor.
     *
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }

    /**
     * @return CallbackTransformer
     */
    public function getModelTransformer()
    {
        return new CallbackTransformer(
            function (ApiUserValue $data) {
                return new UserAccountFieldData($data->login, null, $data->email, $data->enabled);
            },
            function (UserAccountFieldData $submittedData) {
                return $submittedData;
            }
        );
    }
}
