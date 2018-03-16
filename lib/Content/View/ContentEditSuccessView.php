<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\Content\View;

use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

class ContentEditSuccessView extends BaseView
{
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function __construct(Response $response)
    {
        parent::__construct('@EzSystemsRepositoryForms/http/302_empty_content.html.twig');

        $this->setResponse($response);
        $this->setControllerReference(new ControllerReference('ez_content_edit:editVersionDraftSuccessAction'));
    }
}
