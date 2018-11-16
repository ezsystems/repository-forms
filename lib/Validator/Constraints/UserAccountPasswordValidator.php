<?php

declare(strict_types=1);

namespace EzSystems\RepositoryForms\Validator\Constraints;

use EzSystems\RepositoryForms\Data\User\UserAccountFieldData;
use EzSystems\RepositoryForms\Validator\ValidationErrorsProcessor;
use Symfony\Component\Validator\Constraint;

class UserAccountPasswordValidator extends PasswordValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!($value instanceof UserAccountFieldData)) {
            return;
        }

        parent::validate($value->password, $constraint);
    }

    /**
     * {@inheritdoc}
     */
    protected function createValidationErrorsProcessor(): ValidationErrorsProcessor
    {
        return new ValidationErrorsProcessor($this->context, function () {
            return 'password';
        });
    }
}
