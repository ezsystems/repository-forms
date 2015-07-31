<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\Core\FieldType\Page\PageService;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface;
use Symfony\Component\Form\FormInterface;

class PageFormMapper implements FieldTypeFormMapperInterface
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

        $fieldDefinitionForm
            ->add('defaultLayout', 'choice', [
                'choices' => array_combine($availableLayouts, $availableLayouts),
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'property_path' => 'fieldSettings[defaultLayout]',
                'label' => 'field_definition.ezpage.default_layout',
            ]);
    }
}
