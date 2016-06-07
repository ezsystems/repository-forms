<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryFormsBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\RepositoryForms\Data\Mapper\UserRegisterMapper;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\User\UserRegisterType;
use Symfony\Component\HttpFoundation\Request;

class UserRegisterController extends Controller
{
    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var ActionDispatcherInterface
     */
    private $contentActionDispatcher;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var string
     */
    private $pagelayout;

    public function __construct(
        ContentTypeService $contentTypeService,
        UserService $userService,
        ActionDispatcherInterface $contentActionDispatcher,
        Repository $repository
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->contentActionDispatcher = $contentActionDispatcher;
        $this->repository = $repository;
    }

    /**
     * @param string $pagelayout
     * @return ContentEditController
     */
    public function setPagelayout($pagelayout)
    {
        $this->pagelayout = $pagelayout;

        return $this;
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
            throw new \Exception('You are not allowed to register a new account');
        }

        $language = 'eng-GB';

        $contentType = $this->repository->sudo(
            function () {
                return $this->contentTypeService->loadContentTypeByIdentifier('user');
            }
        );

        $data = (new UserRegisterMapper())->mapToFormData($contentType, [
            'mainLanguageCode' => $language,
            'parentGroup' => $this->repository->sudo(
                function () {
                    return $this->userService->loadUserGroup(11);
                }
            ),
        ]);
        $form = $this->createForm(new UserRegisterType(), $data, ['languageCode' => $language]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->contentActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return $this->render('EzSystemsRepositoryFormsBundle:Content:content_edit.html.twig', [
            'form' => $form->createView(),
            'languageCode' => $language,
            'pagelayout' => $this->pagelayout,
        ]);
    }

    public function registerConfirmAction()
    {
        return $this->render(
            '@EzSystemsRepositoryForms/User/register_confirmation.html.twig',
            ['pagelayout' => $this->pagelayout]
        );
    }
}
