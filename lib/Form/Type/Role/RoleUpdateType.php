<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\Role;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for Role update.
 */
class RoleUpdateType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
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
            ->add('identifier', TextType::class, ['label' => 'role.identifier'])
            ->add('saveRole', SubmitType::class, ['label' => 'role.save'])
            ->add('removeDraft', SubmitType::class, ['label' => 'role.remove_draft', 'validation_groups' => false]);
    }
}
