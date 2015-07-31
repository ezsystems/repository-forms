<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Twig;

use eZ\Publish\Core\MVC\Symfony\Templating\Exception\MissingFieldBlockException;
use eZ\Publish\Core\MVC\Symfony\Templating\FieldBlockRendererInterface;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class FieldEditRenderingExtension extends Twig_Extension
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

    public function initRuntime(Twig_Environment $environment)
    {
        $this->fieldBlockRenderer->setTwig($environment);
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction(
                'ez_render_fielddefinition_edit',
                [$this, 'renderFieldDefinitionEdit'],
                ['is_safe' => ['html']]
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
