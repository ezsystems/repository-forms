<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Type;

use EzSystems\RepositoryForms\Form\DataTransformer\DateIntervalToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type for ContentType update.
 */
class DateTimeIntervalType extends AbstractType
{
    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'datetimeinterval';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new DateIntervalToArrayTransformer())
            ->add('year', 'integer')
            ->add('month', 'integer')
            ->add('day', 'integer')
            ->add('hour', 'integer')
            ->add('minute', 'integer')
            ->add('second', 'integer');
    }
}
