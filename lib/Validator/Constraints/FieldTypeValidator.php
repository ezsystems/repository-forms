<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Validator\Constraints;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\Translation\Plural;
use Symfony\Component\Validator\ConstraintValidator;

abstract class FieldTypeValidator extends ConstraintValidator
{
    /**
     * @var FieldTypeService
     */
    protected $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * @param \eZ\Publish\SPI\FieldType\ValidationError[] $validationErrors
     */
    protected function processValidationErrors(array $validationErrors)
    {
        if (empty($validationErrors)) {
            return;
        }

        foreach ($validationErrors as $error) {
            $message = $error->getTranslatableMessage();
            /** @var \Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface $violationBuilder */
            $violationBuilder = $this->context->buildViolation($message instanceof Plural ? $message->plural : $message->message);
            foreach ($message->values as $parameter => $parameterValue) {
                $violationBuilder->setParameter("%$parameter%", $parameterValue);
            }

            $violationBuilder
                // TODO: We need a target under validatorConfigurationHash to clearly identify the field violation is for.
                // Needs to be defined in each FieldType as part of ValidationError (e.g. "target" property containing the property path)
                //->atPath('validatorConfiguration[StringLengthValidator][minStringLength]')
                ->addViolation();
        }
    }
}
