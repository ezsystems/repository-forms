<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Type\Role;

use EzSystems\RepositoryForms\Limitation\LimitationFormMapperRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LimitationType extends AbstractType
{
    /**
     * @var LimitationFormMapperRegistryInterface
     */
    private $limitationFormMapperRegistry;

    public function __construct(LimitationFormMapperRegistryInterface $limitationFormMapperRegistry)
    {
        $this->limitationFormMapperRegistry = $limitationFormMapperRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', 'hidden');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \eZ\Publish\API\Repository\Values\User\Limitation $data */
            $data = $event->getData();
            $form = $event->getForm();
            $this->limitationFormMapperRegistry->getMapper($data->getIdentifier())->mapLimitationForm($form, $data);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => '\eZ\Publish\API\Repository\Values\User\Limitation',
            'translation_domain' => 'ezrepoforms_role',
        ]);
    }

    public function getName()
    {
        return 'ezrepoforms_policy_limitation_edit';
    }
}
