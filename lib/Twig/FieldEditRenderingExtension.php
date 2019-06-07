<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Twig;

use eZ\Publish\Core\MVC\Symfony\Templating\Exception\MissingFieldBlockException;
use eZ\Publish\Core\MVC\Symfony\Templating\FieldBlockRendererInterface;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FieldEditRenderingExtension extends AbstractExtension
{
    /**
     * @var FieldBlockRendererInterface|\eZ\Publish\Core\MVC\Symfony\Templating\Twig\FieldBlockRenderer
     */
    private $fieldBlockRenderer;

    public function __construct(FieldBlockRendererInterface $fieldBlockRenderer)
    {
        $this->fieldBlockRenderer = $fieldBlockRenderer;
    }

    public function getName()
    {
        return 'ezrepoforms.field_edit_rendering';
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction(
                'ez_render_field_definition_edit',
                function (Environment $twig, FieldDefinitionData $fieldDefinitionData, array $params = []) {
                    $this->fieldBlockRenderer->setTwig($twig);

                    return $this->renderFieldDefinitionEdit($fieldDefinitionData, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        );
    }

    public function renderFieldDefinitionEdit(FieldDefinitionData $fieldDefinitionData, array $params = [])
    {
        $params += ['data' => $fieldDefinitionData];
        try {
            return $this->fieldBlockRenderer->renderFieldDefinitionEdit($fieldDefinitionData->fieldDefinition, $params);
        } catch (MissingFieldBlockException $e) {
            // Silently fail on purpose.
            // If there is no template block for current field definition, there might not be anything specific to add.
            return '';
        }
    }
}
