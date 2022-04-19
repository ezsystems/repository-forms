<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\RepositoryFormsBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException as CoreUnauthorizedException;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use EzSystems\RepositoryForms\Data\Mapper\UserCreateMapper;
use EzSystems\RepositoryForms\Data\Mapper\UserUpdateMapper;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\User\UserCreateType;
use EzSystems\RepositoryForms\Form\Type\User\UserUpdateType;
use EzSystems\RepositoryForms\User\View\UserCreateView;
use EzSystems\RepositoryForms\User\View\UserUpdateView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

class UserController extends Controller
{
    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var UserService */
    private $userService;

    /** @var LocationService */
    private $locationService;

    /** @var LanguageService */
    private $languageService;

    /** @var ActionDispatcherInterface */
    private $userActionDispatcher;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    public function __construct(
        ContentTypeService $contentTypeService,
        UserService $userService,
        LocationService $locationService,
        LanguageService $languageService,
        ActionDispatcherInterface $userActionDispatcher,
        PermissionResolver $permissionResolver,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->locationService = $locationService;
        $this->languageService = $languageService;
        $this->userActionDispatcher = $userActionDispatcher;
        $this->permissionResolver = $permissionResolver;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
    }

    /**
     * Displays and processes a user creation form.
     *
     * @param string $contentTypeIdentifier ContentType id to create
     * @param string $language Language code to create the content in (eng-GB, ger-DE, ...))
     * @param int $parentLocationId Location the content should be a child of
     *
     * @return UserCreateView|Response
     *
     * @throws InvalidArgumentType
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     * @throws UndefinedOptionsException
     * @throws OptionDefinitionException
     * @throws NoSuchOptionException
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws AccessException
     * @throws NotFoundException
     */
    public function createAction(
        string $contentTypeIdentifier,
        string $language,
        int $parentLocationId,
        Request $request
    ) {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $contentTypeIdentifier,
            $this->userLanguagePreferenceProvider->getPreferredLanguages()
        );
        $location = $this->locationService->loadLocation($parentLocationId);
        $language = $this->languageService->loadLanguage($language);
        $parentGroup = $this->userService->loadUserGroup($location->contentId);

        $data = (new UserCreateMapper())->mapToFormData($contentType, [$parentGroup], [
            'mainLanguageCode' => $language->languageCode,
        ]);
        $form = $this->createForm(UserCreateType::class, $data, [
            'languageCode' => $language->languageCode,
            'mainLanguageCode' => $language->languageCode,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && null !== $form->getClickedButton()) {
            $this->userActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->userActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new UserCreateView(
            null, [
                'form' => $form->createView(),
                'language' => $language,
                'parentLocation' => $location,
                'contentType' => $contentType,
                'parentGroup' => $parentGroup,
            ]
        );
    }

    /**
     * Displays a user update form that updates user data and related content item.
     *
     * @param int $contentId ContentType id to create
     * @param int $versionNo Version number the version should be created from. Defaults to the currently published one.
     * @param string $language Language code to create the version in (eng-GB, ger-DE, ...))
     *
     * @return UserUpdateView|Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function editAction(
        int $contentId,
        int $versionNo,
        string $language,
        Request $request
    ) {
        $user = $this->userService->loadUser($contentId);
        if (!$this->permissionResolver->canUser('content', 'edit', $user)) {
            throw new CoreUnauthorizedException('content', 'edit', ['userId' => $contentId]);
        }
        $contentType = $this->contentTypeService->loadContentType(
            $user->contentInfo->contentTypeId,
            $this->userLanguagePreferenceProvider->getPreferredLanguages()
        );

        $userUpdate = (new UserUpdateMapper())->mapToFormData($user, $contentType, [
            'languageCode' => $language,
        ]);
        $form = $this->createForm(
            UserUpdateType::class,
            $userUpdate,
            [
                'languageCode' => $language,
                'mainLanguageCode' => $user->contentInfo->mainLanguageCode,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && null !== $form->getClickedButton()) {
            $this->userActionDispatcher->dispatchFormAction($form, $userUpdate, $form->getClickedButton()->getName());
            if ($response = $this->userActionDispatcher->getResponse()) {
                return $response;
            }
        }

        try {
            // assume main location if no location was provided
            $location = $this->locationService->loadLocation(
                (int)$user->versionInfo->contentInfo->mainLocationId
            );
        } catch (UnauthorizedException $e) {
            // if no access to the main location assume content has multiple locations and first of them can be used
            $availableLocations = $this->locationService->loadLocations(
                $user->versionInfo->contentInfo
            );
            $location = array_shift($availableLocations);
        }

        $parentLocation = null;
        try {
            $parentLocation = $this->locationService->loadLocation($location->parentLocationId);
        } catch (UnauthorizedException $e) {
        }

        return new UserUpdateView(
            null, [
                'form' => $form->createView(),
                'languageCode' => $language,
                'language' => $this->languageService->loadLanguage($language),
                'contentType' => $contentType,
                'user' => $user,
                'location' => $location,
                'parentLocation' => $parentLocation,
            ]
        );
    }
}
