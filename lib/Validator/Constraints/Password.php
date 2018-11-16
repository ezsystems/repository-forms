<?php

declare(strict_types=1);

namespace EzSystems\RepositoryForms\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Password extends Constraint
{
    /** @var string */
    public $message = 'ez.user.password.invalid';

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType */
    public $contentType = null;

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
