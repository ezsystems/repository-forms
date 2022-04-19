<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Validator\Constraints;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\URLService;
use EzSystems\RepositoryForms\Data\URL\URLUpdateData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueURLValidator extends ConstraintValidator
{
    /** @var URLService */
    private $urlService;

    /**
     * UniqueURLValidator constructor.
     */
    public function __construct(URLService $urlService)
    {
        $this->urlService = $urlService;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof URLUpdateData || $value->url === null) {
            return;
        }

        try {
            $url = $this->urlService->loadByUrl($value->url);

            if ($url->id === $value->id) {
                return;
            }

            $this->context->buildViolation($constraint->message)
                ->atPath('url')
                ->setParameter('%url%', $value->url)
                ->addViolation();
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
