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
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\Content\ContentCreateType;
use EzSystems\RepositoryForms\View\ContentEdit\ContentEditSuccessView;
use EzSystems\RepositoryForms\View\ContentEdit\ContentEditView;
use Symfony\Component\HttpFoundation\Request;

class ContentEditController extends Controller
{
    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * @var ActionDispatcherInterface
     */
    private $contentActionDispatcher;

    public function __construct(
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        LocationService $locationService,
        ActionDispatcherInterface $contentActionDispatcher
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->contentActionDispatcher = $contentActionDispatcher;
        $this->contentService = $contentService;
    }

    public function createWithoutDraftAction(ContentEditView $view)
    {
        $view->setTemplateIdentifier('EzSystemsRepositoryFormsBundle:Content:content_edit.html.twig');

        return $view;
    }

    public function createAction($contentTypeId, $language, $parentLocationId)
    {
    }

    /**
     * Creates a content draft and then redirects to content edit.
     * Draft creation ONLY occurs when using POST, for the sake of HTTP compliance (a change is done in the repository).
     * If a GET request is incoming, a form will be displayed with a single button, allowing to do the needed POST.
     *
     * @param Request $request
     * @param mixed $contentId
     * @param string $language
     * @param array $params Hash of arbitrary parameters to pass to the default template.
     *                      If "template" key is provided, it will be used for rendering.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createDraftAction(Request $request, $contentId, $language, array $params = [])
    {
        $form = $this->createForm(new ContentCreateType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $contentDraft = $this->contentService->createContentDraft($this->contentService->loadContentInfo($contentId));

            return $this->redirectToRoute('ez_content_edit', [
                'contentId' => $contentId,
                'version' => $contentDraft->getVersionInfo()->versionNo,
                'language' => $language,
            ]);
        }

        $template = isset($params['template']) ? $params['template'] : 'EzSystemsRepositoryFormsBundle:Content:content_create_draft.html.twig';

        return $this->render($template, $params + [
            'contentId' => $contentId,
            'language' => $language,
            'form' => $form->createView(),
        ]);
    }

    public function editAction(ContentEditView $view)
    {
        $view->setTemplateIdentifier('EzSystemsRepositoryFormsBundle:Content:content_edit.html.twig');

        return $view;
    }

    public function editSuccessAction(ContentEditSuccessView $view)
    {
        // Either check if $view has a Response, and return that response
        // Or use the contentActionDispatcher right here, and do something with the response
        //   In that case, the ContentEditSuccessView is pretty much a ContentEditView with an extra flag

        // I'd go for the later, as below. It makes more sense since
        $this->contentActionDispatcher->dispatchFormAction(
            $view->getForm(),
            $view->getForm()->getData(),
            $view->getForm()->getClickedButton()->getName()
        );
        if ($response = $this->contentActionDispatcher->getResponse()) {
            return $response;
        } else {
            return $view;
        }
    }
}
