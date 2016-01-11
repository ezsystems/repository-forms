<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Type\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentCreateType extends AbstractType
{
    public function getName()
    {
        return 'ezrepoforms_content_create';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('create', 'submit', ['label' => $options['new_content'] ? 'content.create_button' : 'content.edit_button']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'new_content' => false,
                'translation_domain' => 'ezrepoforms_content',
            ]);
    }
}
