<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Twig;

use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\RepositoryForms\Limitation\Templating\LimitationBlockRenderer;
use EzSystems\RepositoryForms\Limitation\Templating\LimitationBlockRendererInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LimitationValueRenderingExtension extends AbstractExtension
{
    /** @var LimitationBlockRenderer */
    private $limitationRenderer;

    /**
     * LimitationValueRenderingExtension constructor.
     *
     * @param LimitationBlockRendererInterface $limitationRenderer
     */
    public function __construct(LimitationBlockRendererInterface $limitationRenderer)
    {
        $this->limitationRenderer = $limitationRenderer;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_render_limitation_value',
                function (Environment $twig, Limitation $limitation, array $params = []) {
                    return $this->limitationRenderer->renderLimitationValue($limitation, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    public function getName()
    {
        return 'ezrepoforms.limitation_value_rendering';
    }
}
