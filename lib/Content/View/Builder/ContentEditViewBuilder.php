<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\Content\View\Builder;

use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use EzSystems\RepositoryForms\Content\View\ContentEditSuccessView;
use EzSystems\RepositoryForms\Content\View\ContentEditView;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;

/**
 * Builds ContentEditView objects.
 *
 * @internal
 */
class ContentEditViewBuilder implements ViewBuilder
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    /** @var string */
    private $defaultTemplate;

    /** @var \EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface */
    private $contentActionDispatcher;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $languagePreferenceProvider;

    public function __construct(
        Repository $repository,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector,
        string $defaultTemplate,
        ActionDispatcherInterface $contentActionDispatcher,
        UserLanguagePreferenceProviderInterface $languagePreferenceProvider
    ) {
        $this->repository = $repository;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
        $this->defaultTemplate = $defaultTemplate;
        $this->contentActionDispatcher = $contentActionDispatcher;
        $this->languagePreferenceProvider = $languagePreferenceProvider;
    }

    public function matches($argument)
    {
        return 'ez_content_edit:editVersionDraftAction' === $argument;
    }

    /**
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView|\eZ\Publish\Core\MVC\Symfony\View\View
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function buildView(array $parameters)
    {
        // @todo improve default templates injection
        $view = new ContentEditView($this->defaultTemplate);

        $language = $this->resolveLanguage($parameters);
        $location = $this->resolveLocation($parameters);
        $content = $this->resolveContent($parameters, $location, $language);
        $contentInfo = $content->contentInfo;
        $contentType = $this->loadContentType((int) $contentInfo->contentTypeId, $this->languagePreferenceProvider->getPreferredLanguages());
        $form = $parameters['form'];
        $isPublished = null !== $contentInfo->mainLocationId && $contentInfo->published;

        if (!$content->getVersionInfo()->isDraft()) {
            throw new InvalidArgumentException('Version', 'status is not draft');
        }

        if (null === $location && $isPublished) {
            try {
                // assume main location if no location was provided
                $location = $this->loadLocation((int) $contentInfo->mainLocationId);
            } catch (UnauthorizedException $e) {
                // if no access to the main location assume content has multiple locations and first of them can be used
                $availableLocations = $this->repository->getLocationService()->loadLocations($contentInfo);
                $location = array_shift($availableLocations);
            }
        }

        if (null !== $location && $location->contentId !== $content->id) {
            throw new InvalidArgumentException('Location', 'Provided location does not belong to selected content');
        }

        if ($form->isValid() && null !== $form->getClickedButton()) {
            $this->contentActionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                $form->getClickedButton()->getName(),
                ['referrerLocation' => $location]
            );

            if ($response = $this->contentActionDispatcher->getResponse()) {
                $view = new ContentEditSuccessView($response);
                $view->setLocation($location);

                return $view;
            }
        }

        $view->setContent($content);
        $view->setLanguage($language);
        $view->setLocation($location);
        $view->setForm($parameters['form']);

        $view->addParameters([
            'content' => $content,
            'location' => $location,
            'language' => $language,
            'contentType' => $contentType,
            'form' => $form->createView(),
        ]);

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    /**
     * Loads Content with id $contentId.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadContent(int $contentId, array $languages = [], int $versionNo = null): Content
    {
        return $this->repository->getContentService()->loadContent($contentId, $languages, $versionNo);
    }

    /**
     * Loads a visible Location.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadLocation(int $locationId): Location
    {
        return $this->repository->getLocationService()->loadLocation($locationId);
    }

    /**
     * Loads Language with code $languageCode.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadLanguage(string $languageCode): Language
    {
        return $this->repository->getContentLanguageService()->loadLanguage($languageCode);
    }

    /**
     * Loads ContentType with id $contentTypeId.
     *
     * @param string[] $languageCodes
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadContentType(int $contentTypeId, array $languageCodes): ContentType
    {
        return $this->repository->getContentTypeService()->loadContentType($contentTypeId, $languageCodes);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function resolveLanguage(array $parameters): Language
    {
        if (isset($parameters['languageCode'])) {
            return $this->loadLanguage($parameters['languageCode']);
        }

        if (isset($parameters['language'])) {
            if (\is_string($parameters['language'])) {
                // @todo BC: route parameter should be called languageCode but it won't happen until 3.0
                return $this->loadLanguage($parameters['language']);
            }

            return $parameters['language'];
        }

        throw new InvalidArgumentException('Language', 'No language information provided. Are you missing language or languageCode parameters');
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function resolveContent(array $parameters, ?Location $location, Language $language): Content
    {
        if (isset($parameters['content'])) {
            return $parameters['content'];
        }

        if (isset($parameters['contentId'])) {
            $contentId = $parameters['contentId'];
        } elseif (null !== $location) {
            $contentId = $location->contentId;
        } else {
            throw new InvalidArgumentException('Content', 'No content could be loaded from parameters');
        }

        return $this->loadContent(
            (int) $contentId,
            null !== $language ? [$language->languageCode] : [],
            (int) $parameters['versionNo'] ?: null
        );
    }

    private function resolveLocation(array $parameters): ?Location
    {
        if (isset($parameters['locationId'])) {
            try {
                // the load error is suppressed because a user can have no permission to this location
                // but can have access to another location when content is in multiple locations
                return $this->loadLocation((int) $parameters['locationId']);
            } catch (UnauthorizedException $e) {
            }
        }

        if (isset($parameters['location'])) {
            return $parameters['location'];
        }

        return null;
    }
}
