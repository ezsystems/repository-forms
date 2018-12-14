<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\Core\FieldType\Page\PageService;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageFormMapper implements FieldDefinitionFormMapperInterface
{
    /**
     * @var PageService Provides layout list used in form selector
     */
    protected $pageService;

    /**
     * @param PageService $pageService
     */
    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $availableLayouts = $this->pageService->getAvailableZoneLayouts();
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;

        $fieldDefinitionForm
            ->add('defaultLayout', ChoiceType::class, [
                'choices' => array_combine($availableLayouts, $availableLayouts),
                'choices_as_values' => true,
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'property_path' => 'fieldSettings[defaultLayout]',
                'label' => 'field_definition.ezpage.default_layout',
                'disabled' => $isTranslation,
            ]);
    }

    /**
     * Fake method to set the translation domain for the extractor.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }
}
