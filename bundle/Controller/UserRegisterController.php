<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use EzSystems\EzPlatformUserBundle\Controller\UserRegisterController as BaseUserRegisterController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated Deprecated in 2.5 and will be removed in 3.0. Please use \EzSystems\EzPlatformUserBundle\Controller\UserRegisterController instead.
 */
class UserRegisterController extends Controller
{
    /** @var \EzSystems\EzPlatformUserBundle\Controller\UserRegisterController */
    private $userRegisterController;

    /**
     * @param \EzSystems\EzPlatformUserBundle\Controller\UserRegisterController $userRegisterController
     */
    public function __construct(BaseUserRegisterController $userRegisterController)
    {
        $this->userRegisterController = $userRegisterController;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformUser\View\Register\FormView|\Symfony\Component\HttpFoundation\Response|null
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function registerAction(Request $request)
    {
        return $this->userRegisterController->registerAction($request);
    }

    /**
     * @return \EzSystems\EzPlatformUser\View\Register\ConfirmView
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function registerConfirmAction()
    {
        return $this->userRegisterController->registerConfirmAction();
    }
}
