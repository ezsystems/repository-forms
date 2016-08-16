<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\Section;

use eZ\Publish\API\Repository\SectionService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SectionDeleteType extends AbstractType
{
    /**
     * @var SectionService
     */
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_section_delete';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sectionId', HiddenType::class, [
                'constraints' => new Callback(
                    function ($sectionId, ExecutionContextInterface $context) {
                        $contentCount = $this->sectionService->countAssignedContents(
                            $this->sectionService->loadSection($sectionId)
                        );

                        if ($contentCount > 0) {
                            $context
                                ->buildViolation('section.error.cannot_delete_with_assigned_content')
                                ->addViolation();
                        }
                    }
                ),
            ])
            ->add('delete', SubmitType::class, ['label' => 'section.form.delete']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['translation_domain' => 'ezrepoforms_section']);
    }
}
