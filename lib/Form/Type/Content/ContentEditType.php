<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_content_edit';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fieldsData', CollectionType::class, [
                'entry_type' => ContentFieldType::class,
                'label' => 'ezrepoforms.content.fields',
                'entry_options' => [
                    'languageCode' => $options['languageCode'],
                    'mainLanguageCode' => $options['mainLanguageCode'],
                ],
            ])
            ->add('redirectUrlAfterPublish', HiddenType::class, ['required' => false, 'mapped' => false])
            ->add('publish', SubmitType::class, ['label' => 'content.publish_button']);

        if ($options['drafts_enabled']) {
            $builder
                ->add('saveDraft', SubmitType::class, ['label' => 'content.save_button'])
                ->add('cancel', SubmitType::class, [
                    'label' => 'content.cancel_button',
                    'attr' => ['formnovalidate' => 'formnovalidate'],
                ]);
            $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'suppressValidationOnCancel'], 900);
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['languageCode'] = $options['languageCode'];
        $view->vars['mainLanguageCode'] = $options['mainLanguageCode'];
    }

    public function suppressValidationOnCancel(FormEvent $event)
    {
        $form = $event->getForm();

        if ($form->get('cancel')->isClicked()) {
            $event->stopPropagation();
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
            ->setRequired(['languageCode', 'mainLanguageCode']);
    }
}
