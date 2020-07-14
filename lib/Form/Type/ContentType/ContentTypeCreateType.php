<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\ContentType;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ContentTypeCreateType extends AbstractType
{
    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_contenttype_create';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contentTypeGroupId', HiddenType::class, [
                'constraints' => new Callback(
                    function ($contentTypeGroupId, ExecutionContextInterface $context) {
                        try {
                            $this->contentTypeService->loadContentTypeGroup($contentTypeGroupId);
                        } catch (NotFoundException $e) {
                            $context
                                ->buildViolation('content_type.error.content_type_group.not_found')
                                ->setParameter('%id%', $contentTypeGroupId)
                                ->addViolation();
                        }
                    }
                ),
            ])
            ->add('name', TextType::class, [
                'label' => 'content_type.name',
            ])
            ->add('identifier', TextType::class, [
                'label' => 'content_type.identifier',
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'content_type.description',
            ])
            ->add('nameSchema', TextType::class, [
                'required' => false,
                'label' => 'content_type.name_schema',
            ])
            ->add('urlAliasSchema', TextType::class, [
                'required' => false,
                'label' => 'content_type.url_alias_schema',
            ])
            ->add('isContainer', CheckboxType::class, [
                'required' => false,
                'label' => 'content_type.is_container',
            ])
            ->add('defaultSortField', SortFieldChoiceType::class, [
                'label' => 'content_type.default_sort_field',
            ])
            ->add('defaultSortOrder', SortOrderChoiceType::class, [
                'label' => 'content_type.default_sort_order',
            ])
            ->add('defaultAlwaysAvailable', CheckboxType::class, [
                'required' => false,
                'label' => 'content_type.default_always_available',
            ])
            ->add('fieldTypeSelection', FieldTypeChoiceType::class, [
                'label' => 'content_type.field_type_selection',
            ])
            ->add('publishContentType', SubmitType::class, ['label' => 'content_type.create'])
            ->add('addFieldDefinition', SubmitType::class, [
                'label' => 'content_type.add_field_definition',
            ])
        ;
    }
}
