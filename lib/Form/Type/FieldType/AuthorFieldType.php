<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldType;

use eZ\Publish\Core\FieldType\Author\Author;
use eZ\Publish\Core\FieldType\Author\Value;
use EzSystems\RepositoryForms\Form\Type\FieldType\Author\AuthorCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezauthor field type.
 */
class AuthorFieldType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezauthor';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('authors', AuthorCollectionType::class, [])
            ->addViewTransformer($this->getViewTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Value::class]);
    }

    /**
     * Returns a view transformer which handles empty row needed to display add/remove buttons.
     *
     * @return DataTransformerInterface
     */
    public function getViewTransformer(): DataTransformerInterface
    {
        return new CallbackTransformer(function (Value $value) {
            if (0 === $value->authors->count()) {
                $value->authors->append(new Author());
            }

            return $value;
        }, function (Value $value) {
            $value->authors->exchangeArray(
                array_filter(
                    $value->authors->getArrayCopy(),
                    function (Author $author) {
                        return !empty($author->email);
                    }
                )
            );

            return $value;
        });
    }
}
