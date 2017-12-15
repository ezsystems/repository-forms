<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use EzSystems\RepositoryForms\Data\Mapper\UserRegisterMapper;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\User\UserRegisterType;
use EzSystems\RepositoryForms\UserRegister\View\UserRegisterConfirmView;
use EzSystems\RepositoryForms\UserRegister\View\UserRegisterFormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserRegisterController extends Controller
{
    /**
     * @var UserRegisterMapper
     */
    private $userRegisterMapper;

    /**
     * @var ActionDispatcherInterface
     */
    private $contentActionDispatcher;

    public function __construct(
        UserRegisterMapper $userRegisterMapper,
        ActionDispatcherInterface $contentActionDispatcher
    ) {
        $this->userRegisterMapper = $userRegisterMapper;
        $this->contentActionDispatcher = $contentActionDispatcher;
    }

    /**
     * Displays and processes a user registration form.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception if the current user isn't allowed to register an account
     */
    public function registerAction(Request $request)
    {
        if (!$this->isGranted(new Attribute('user', 'register'))) {
            throw new UnauthorizedHttpException('You are not allowed to register a new account');
        }

        $data = $this->userRegisterMapper->mapToFormData();
        $language = $data->mainLanguageCode;
        $form = $this->createForm(
            UserRegisterType::class,
            $data,
            ['languageCode' => $language]
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->contentActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new UserRegisterFormView(
            null,
            ['form' => $form->createView()]
        );
    }

    public function registerConfirmAction()
    {
        return new UserRegisterConfirmView();
    }
}
