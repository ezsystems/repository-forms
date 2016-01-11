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

/**
 * Form type for content edition (create/update).
 * Underlying data will be either \EzSystems\RepositoryForms\Data\Content\ContentCreateData or \EzSystems\RepositoryForms\Data\Content\ContentUpdateData
 * depending on the context (create or update).
 */
class ContentEditType extends AbstractType
{
    public function getName()
    {
        return 'ezrepoforms_content_edit';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fieldsData', 'collection', [
                'type' => 'ezrepoforms_content_field',
                'label' => 'ezrepoforms.content.fields',
                'options' => ['languageCode' => $options['languageCode']],
            ])
            ->add('redirectUrlAfterPublish', 'hidden', ['required' => false, 'mapped' => false])
            ->add('publish', 'submit', ['label' => 'content.publish_button']);

        if ($options['drafts_enabled']) {
            $builder
                ->add('saveDraft', 'submit', ['label' => 'content.save_button'])
                ->add('cancel', 'submit', [
                    'label' => 'content.cancel_button',
                    'attr' => ['formnovalidate' => 'formnovalidate'],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'drafts_enabled' => false,
                'data_class' => '\eZ\Publish\API\Repository\Values\Content\ContentStruct',
                'translation_domain' => 'ezrepoforms_content',
            ])
            ->setRequired(['languageCode']);
    }
}
