<?php

declare(strict_types=1);

namespace EzSystems\RepositoryForms\Validator\Constraints;

use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\PasswordValidationContext;
use EzSystems\RepositoryForms\Validator\ValidationErrorsProcessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordValidator extends ConstraintValidator
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /**
     * @param \eZ\Publish\API\Repository\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!\is_string($value) || empty($value)) {
            return;
        }

        $passwordValidationContext = new PasswordValidationContext([
            'contentType' => $constraint->contentType,
        ]);

        $validationErrors = $this->userService->validatePassword($value, $passwordValidationContext);
        if (!empty($validationErrors)) {
            $validationErrorsProcessor = $this->createValidationErrorsProcessor();
            $validationErrorsProcessor->processValidationErrors($validationErrors);
        }
    }

    /**
     * @return \EzSystems\RepositoryForms\Validator\ValidationErrorsProcessor
     */
    protected function createValidationErrorsProcessor(): ValidationErrorsProcessor
    {
        return new ValidationErrorsProcessor($this->context);
    }
}
