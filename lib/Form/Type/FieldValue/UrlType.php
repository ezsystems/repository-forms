<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\RepositoryForms\Form\Type\FieldValue;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType as CoreUrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UrlType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_fieldtype_url';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'url',
                CoreUrlType::class,
                ['label' => 'URL', 'property_path' => 'link', 'required' => $options['required']])
            ->add(
                'text',
                TextType::class,
                ['label' => 'Text', 'property_path' => 'text', 'required' => false]);
    }
}
