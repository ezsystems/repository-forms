<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function getName()
    {
        return 'ezuser';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', [
                'property_path' => 'username',
                'label' => 'content.field_type.ezuser.username',
                'required' => $options['required'],
            ])
            ->add('password', 'repeated', [
                'type' => 'password',
                'required' => true,
                'property_path' => 'password',
                'first_options' => ['label' => 'content.field_type.ezuser.password'],
                'second_options' => ['label' => 'content.field_type.ezuser.password_confirm'],
            ])
            ->add('email', 'email', [
                'label' => 'content.field_type.ezuser.email',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => '\EzSystems\RepositoryForms\Data\User\UserAccountFieldData',
                'translation_domain' => 'ezrepoforms_content',
            ]);
    }
}
