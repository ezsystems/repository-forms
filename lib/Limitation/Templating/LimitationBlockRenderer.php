<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Limitation\Templating;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\RepositoryForms\Limitation\Exception\MissingLimitationBlockException;
use EzSystems\RepositoryForms\Limitation\Exception\ValueMapperNotFoundException;
use EzSystems\RepositoryForms\Limitation\LimitationValueMapperRegistryInterface;
use Twig_Environment;
use Twig_Template;

class LimitationBlockRenderer implements LimitationBlockRendererInterface
{
    const LIMITATION_VALUE_BLOCK_NAME = 'ez_limitation_%s_value';
    const LIMITATION_VALUE_BLOCK_NAME_FALLBACK = 'ez_limitation_value_fallback';

    /**
     * @var LimitationValueMapperRegistryInterface
     */
    private $valueMapperRegistry;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var array
     */
    private $limitationValueResources = [];

    /**
     * LimitationRenderer constructor.
     */
    public function __construct(LimitationValueMapperRegistryInterface $valueMapperRegistry, Twig_Environment $twig)
    {
        $this->valueMapperRegistry = $valueMapperRegistry;
        $this->twig = $twig;
    }

    public function renderLimitationValue(Limitation $limitation, array $parameters = [])
    {
        try {
            $blockName = $this->getValueBlockName($limitation);
            $parameters = $this->getValueBlockParameters($limitation, $parameters);
        } catch (ValueMapperNotFoundException | NotFoundException $exception) {
            $blockName = self::LIMITATION_VALUE_BLOCK_NAME_FALLBACK;
            $parameters = $this->getValueFallbackBlockParameters($limitation, $parameters);
        }

        $localTemplate = null;
        if (isset($parameters['template'])) {
            $localTemplate = $parameters['template'];
            unset($parameters['template']);
        }

        $template = $this->findTemplateWithBlock($blockName, $localTemplate);
        if ($template === null) {
            throw new MissingLimitationBlockException("Could not find block for {$limitation->getIdentifier()}: $blockName!");
        }

        return $template->renderBlock($blockName, $parameters);
    }

    public function setLimitationValueResources(array $resources)
    {
        usort($resources, function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        $this->limitationValueResources = array_column($resources, 'template');
    }

    /**
     * Generates value block name based on Limitation.
     *
     * @return string
     */
    protected function getValueBlockName(Limitation $limitation)
    {
        return sprintf(self::LIMITATION_VALUE_BLOCK_NAME, strtolower($limitation->getIdentifier()));
    }

    /**
     * Find the first template containing block definition $blockName.
     *
     * @param string $blockName
     * @param string|Twig_Template $localTemplate
     * @return \Twig_TemplateWrapper|null
     */
    protected function findTemplateWithBlock($blockName, $localTemplate = null)
    {
        if ($localTemplate !== null) {
            if (\is_string($localTemplate)) {
                $localTemplate = $this->twig->load($localTemplate);
            }

            if ($localTemplate->hasBlock($blockName)) {
                return $localTemplate;
            }
        }

        foreach ($this->limitationValueResources as &$template) {
            if (\is_string($template)) {
                // Load the template if it is necessary
                $template = $this->twig->load($template);
            }

            if ($template->hasBlock($blockName)) {
                return $template;
            }
        }

        return null;
    }

    /**
     * Get parameters passed as context of value block render.
     *
     * @return array
     */
    protected function getValueBlockParameters(Limitation $limitation, array $parameters)
    {
        $values = $this->valueMapperRegistry
            ->getMapper($limitation->getIdentifier())
            ->mapLimitationValue($limitation);

        $parameters += [
            'limitation' => $limitation,
            'values' => $values,
        ];

        return $parameters;
    }

    /**
     * Get parameters passed as context of value fallback block.
     *
     * @return array
     */
    protected function getValueFallbackBlockParameters(Limitation $limitation, array $parameters)
    {
        $parameters += [
            'limitation' => $limitation,
            'values' => $limitation->limitationValues,
        ];

        return $parameters;
    }
}
