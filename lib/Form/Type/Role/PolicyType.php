<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Type\Role;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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

    public function __construct(array $policyMap, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->policyChoices = $this->buildPolicyChoicesFromMap($policyMap);
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
        $policyChoices = ['role.policy.all_modules' => ['*|*' => 'role.policy.all_modules_all_functions']];
        foreach ($policyMap as $module => $functionList) {
            $humanizedModule = $this->humanize($module);
            // For each module, add possibility to grant access to all functions.
            $policyChoices[$humanizedModule] = [
                "$module|*" => "$humanizedModule / " . $this->translator->trans('role.policy.all_functions', [], 'ezrepoforms_role'),
            ];

            foreach ($functionList as $function => $limitationList) {
                $policyChoices[$humanizedModule]["$module|$function"] = $humanizedModule . ' / ' . $this->humanize($function);
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
            ->add('moduleFunction', 'choice', [
                'choices' => $this->policyChoices,
                'label' => 'role.policy.type',
                'placeholder' => 'role.policy.type.choose',
            ])
            ->add('removeDraft', 'submit', ['label' => 'role.cancel', 'validation_groups' => false])
            ->add('savePolicy', 'submit', ['label' => 'role.save']);
    }

    public function getName()
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
