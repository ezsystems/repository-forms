<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Type\Role;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for Role update.
 */
class RoleUpdateType extends AbstractType
{
    public function getName()
    {
        return 'ezrepoforms_role_update';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'EzSystems\RepositoryForms\Data\Role\RoleData',
                'translation_domain' => 'ezrepoforms_role',
            ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', 'text', ['label' => 'role.identifier'])
            ->add('saveRole', 'submit', ['label' => 'role.save'])
            ->add('removeDraft', 'submit', ['label' => 'role.remove_draft', 'validation_groups' => false]);
    }
}
