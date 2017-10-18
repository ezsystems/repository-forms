<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldValue\Author;

use eZ\Publish\Core\FieldType\Author\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Combined entry type for ezauthor.
 */
class AuthorEntryType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'ezrepoforms_fieldtype_ezauthor_authors_entry';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'id',
                HiddenType::class,
                [
                    'label' => false,
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'content.field_type.ezauthor.name',
                    'required' => $options['required'],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'content.field_type.ezauthor.email',
                    'required' => $options['required'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', Author::class);
    }
}
