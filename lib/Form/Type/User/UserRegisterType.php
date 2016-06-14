<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for content edition (create/update).
 * Underlying data will be either \EzSystems\RepositoryForms\Data\Content\ContentCreateData or \EzSystems\RepositoryForms\Data\Content\ContentUpdateData
 * depending on the context (create or update).
 */
class UserRegisterType extends AbstractType
{
    public function getName()
    {
        return 'ezrepoforms_user_register';
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
            // @todo add the string to its own domain
            ->add('publish', 'submit', ['label' => 'user.register_button'])
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                array($this, 'mapUserFieldToUserCreate')
            );
    }

    public function mapUserFieldToUserCreate(FormEvent $event)
    {
        $userCreateData = $event->getData();

        if (isset($userCreateData->fieldsData['user_account'])) {
            $userAccountFieldData = $userCreateData->fieldsData['user_account']->value;
            $userCreateData->login = $userAccountFieldData->username;
            $userCreateData->email = $userAccountFieldData->email;
            $userCreateData->password = $userAccountFieldData->password;

            //$userCreateData->setField('user_account', null);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => '\eZ\Publish\API\Repository\Values\User\UserCreateStruct',
                'translation_domain' => 'ezrepoforms_user_registration',
            ])
            ->setRequired(['languageCode']);
    }
}
