<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\Role;

use eZ\Publish\API\Repository\RoleService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class PolicyType extends AbstractType
{
    /**
     * @var array
     */
    private $policyChoices;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RoleService
     */
    private $roleService;

    public function __construct(array $policyMap, TranslatorInterface $translator, RoleService $roleService)
    {
        $this->translator = $translator;
        $this->policyChoices = $this->buildPolicyChoicesFromMap($policyMap);
        $this->roleService = $roleService;
    }

    /**
     * Returns a usable hash for the policy choice widget.
     * Key is the humanized "module" name.
     * Value is a hash with "<module>|<function"> as key and humanized "function" name as value.
     *
     * @return array
     */
    private function buildPolicyChoicesFromMap($policyMap)
    {
        $policyChoices = [
            $this->translator->trans('role.policy.all_modules', [], 'ezrepoforms_role') => [
                $this->translator->trans('role.policy.all_modules_all_functions', [], 'ezrepoforms_role') => '*|*',
            ],
        ];

        foreach ($policyMap as $module => $functionList) {
            $humanizedModule = $this->humanize($module);
            // For each module, add possibility to grant access to all functions.
            $policyChoices[$humanizedModule] = [
                "$humanizedModule / " . $this->translator->trans('role.policy.all_functions', [], 'ezrepoforms_role') => "$module|*",
            ];

            foreach ($functionList as $function => $limitationList) {
                $policyChoices[$humanizedModule][$humanizedModule . ' / ' . $this->humanize($function)] = "$module|$function";
            }
        }

        return $policyChoices;
    }

    /**
     * Makes a technical name human readable.
     *
     * Sequences of underscores are replaced by single spaces. The first letter
     * of the resulting string is capitalized, while all other letters are
     * turned to lowercase.
     *
     * @see \Symfony\Component\Form\FormRenderer::humanize()
     *
     * @param string $text The text to humanize.
     *
     * @return string The humanized text.
     */
    private function humanize($text)
    {
        return ucfirst(trim(strtolower(preg_replace(array('/([A-Z])/', '/[_\s]+/'), array('_$1', ' '), $text))));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('moduleFunction', ChoiceType::class, [
                'choices' => $this->policyChoices,
                'choices_as_values' => true,
                'label' => 'role.policy.type',
                'placeholder' => 'role.policy.type.choose',
            ])
            ->add('removeDraft', SubmitType::class, ['label' => 'role.cancel', 'validation_groups' => false])
            ->add('savePolicy', SubmitType::class, ['label' => 'role.policy.save']);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \EzSystems\RepositoryForms\Data\Role\PolicyCreateData|\EzSystems\RepositoryForms\Data\Role\PolicyUpdateData $data */
            $data = $event->getData();
            $form = $event->getForm();

            if ($module = $data->getModule()) {
                $form
                    ->add('limitationsData', CollectionType::class, [
                        'entry_type' => LimitationType::class,
                        'label' => 'role.policy.available_limitations',
                    ]);
            } else {
                $form->add('saveAndAddLimitation', SubmitType::class, ['label' => 'role.policy.save_and_add_limitation']);
            }
        });
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_policy_edit';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => '\eZ\Publish\API\Repository\Values\User\PolicyStruct',
            'translation_domain' => 'ezrepoforms_role',
        ]);
    }
}
