<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Validator\Constraints;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Will check if ContentType identifier is not already used in the content repository.
 */
class UniqueContentTypeIdentifierValidator extends ConstraintValidator
{
    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function validate($value, Constraint $constraint)
    {
        try {
            $this->contentTypeService->loadContentTypeByIdentifier($value);
            $this->context->buildViolation($constraint->message)
                ->setParameter('%identifier%', $value)
                ->addViolation();
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
