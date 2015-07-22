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

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('identifier', 'text')
            ->add('save', 'submit', ['label' => 'section.form.save'])
            ->add('cancel', 'submit', ['label' => 'section.form.cancel']);
    }

    public function getName()
    {
        return 'ezrepoforms_section_update';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => '\EzSystems\RepositoryForms\Data\SectionData',
            'translation_domain' => 'ezrepoforms_section',
        ]);
    }
}
