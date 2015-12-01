<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\View\ContentEdit;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use EzSystems\RepositoryForms\Data\Mapper\ContentCreateMapper;
use EzSystems\RepositoryForms\Data\Mapper\ContentUpdateMapper;
use EzSystems\RepositoryForms\Form\Type\Content\ContentEditType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Adds, if applicable, the ContentEditForm view to the request.
 */
class ContentEditFormFilter implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /**
     * ContentEditFormFilter constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        FormFactoryInterface $formFactory
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->formFactory = $formFactory;
        $this->locationService = $locationService;
    }

    public static function getSubscribedEvents()
    {
        return [
            ViewEvents::FILTER_BUILDER_PARAMETERS => [
                ['addContentEditForm'],
                ['addContentEditWithoutDraftForm'],
            ],
        ];
    }

    public function addContentEditForm(FilterViewBuilderParametersEvent $e)
    {
        $request = $e->getRequest();

        if ($request->attributes->get('_controller') !== 'ez_content_edit:editAction') {
            return;
        }

        $contentDraft = $this->contentService->loadContent(
            $request->attributes->get('contentId'),
            [$request->attributes->get('language')],
            $request->attributes->get('version')
        );
        $contentType = $this->contentTypeService->loadContentType($contentDraft->contentInfo->contentTypeId);

        $data = (new ContentUpdateMapper())->mapToFormData($contentDraft, [
            'languageCode' => $request->attributes->get('language'),
            'contentType' => $contentType,
        ]);
        $form = $this->formFactory->create(new ContentEditType(), $data, [
            'languageCode' => $request->attributes->get('language'),
            'drafts_enabled' => true,
        ]);

        $e->getParameters()->add(['form', $form->handleRequest($request)]);
    }

    public function addContentEditWithoutDraftForm(FilterViewBuilderParametersEvent $e)
    {
        $request = $e->getRequest();

        if ($request->attributes->get('_controller') !== 'ez_content_edit:createWithoutDraftAction') {
            return;
        }

        $contentType = $this->contentTypeService->loadContentType($request->attributes->get('contentTypeId'));
        $data = (new ContentCreateMapper())->mapToFormData($contentType, [
            'mainLanguageCode' => $request->attributes->get('language'),
            'parentLocation' => $this->locationService->newLocationCreateStruct($request->attributes->get('parentLocationId')),
        ]);
        $form = $this->formFactory->create(new ContentEditType(), $data, ['languageCode' => $request->attributes->get('language')]);
        $form->handleRequest($request);

        $e->getParameters()->add(['form' => $form]);
    }
}
