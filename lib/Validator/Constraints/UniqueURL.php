<?php

namespace EzSystems\RepositoryForms\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueURL extends Constraint
{
    /**
     * %url% placeholder is passed.
     *
     * @var string
     */
    public $message = 'ez.url.unique';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'ezrepoforms.validator.unique_url';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
