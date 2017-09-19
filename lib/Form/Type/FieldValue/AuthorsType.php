<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldValue;

use eZ\Publish\Core\FieldType\Author\Author;
use eZ\Publish\Core\FieldType\Author\Value;
use EzSystems\RepositoryForms\Form\Type\FieldValue\Author\AuthorCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Combined Type for ezauthor.
 */
class AuthorsType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_fieldtype_ezauthor';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('authors', AuthorCollectionType::class, [])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addEmptyEntry']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', Value::class);
    }

    public function addEmptyEntry(FormEvent $event)
    {
        $data = $event->getData();
        $contentStruct = $event->getForm()->getRoot()->getData();

        if ($contentStruct->isNew()) {
            $data->authors->offsetSet(0, new Author(['id' => null, 'name' => null, 'email' => null]));
        }
    }
}
