<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\View\ContentEdit\ParameterFilter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use EzSystems\RepositoryForms\Data\Mapper\ContentUpdateMapper;
use EzSystems\RepositoryForms\Form\Type\Content\ContentEditType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;

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

    /**
     * ContentEditFormFilter constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        FormFactoryInterface $formFactory
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->formFactory = $formFactory;
    }

    public static function getSubscribedEvents()
    {
        return [ViewEvents::FILTER_BUILDER_PARAMETERS => 'addContentEditForm'];
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
}
