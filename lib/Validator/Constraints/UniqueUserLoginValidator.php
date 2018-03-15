<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Validator\Constraints;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserService;
use EzSystems\RepositoryForms\Data\User\UserCreateData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserLoginValidator extends ConstraintValidator
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof UserCreateData || $value->login === null) {
            return;
        }

        try {
            $this->userService->loadUserByLogin($value->login);

            $this->context->buildViolation($constraint->message)
                ->atPath('fieldsData[user_account].value.username')
                ->setParameter('%login%', $value->login)
                ->addViolation();
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
