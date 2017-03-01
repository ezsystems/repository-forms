<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\Controller;

use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\Content\ContentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @property \Symfony\Component\DependencyInjection\ContainerInterface $container
 */
class JsonSchemaController extends Controller
{
    /**
     * @var ActionDispatcherInterface
     */
    private $contentActionDispatcher;

    public function createContentFormAction(Request $request, $contentTypeIdentifier, $parentLocationId, $language = 'eng-GB')
    {
        $contentType = $this->getContentTypeService()->loadContentTypeByIdentifier($contentTypeIdentifier);
        $contentCreateStruct = $this->getContentService()->newContentCreateStruct(
            $contentType,
            $language
        );

        $form = $this->createForm(
            ContentFormType::class,
            $contentCreateStruct,
            [
                'languageCode' => $language,
                'csrf_protection' => false,
                'enable_controls' => false,
                'parentLocationId' => $parentLocationId,
                'label' => "New " . $contentType->getName($language)
            ]
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->contentActionDispatcher->dispatchFormAction(
                $form,
                $contentCreateStruct,
                $form->getClickedButton()->getName()
            );

            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        $this->configureResolver();

        $response = new JsonResponse(
            $this->get('liform')->transform($form),
            200,
            ['Content-Type' => 'application/schema+json']
        );
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);

        return $response;
    }

        public function createContentFromJsonAction(Request $request, $contentTypeIdentifier, $parentLocationId, $language = 'eng-GB')
    {
        $contentType = $this->getContentTypeService()->loadContentTypeByIdentifier($contentTypeIdentifier);
        $contentCreateStruct = $this->getContentService()->newContentCreateStruct(
            $contentType,
            $language
        );

        $payload = json_decode($request->getContent(), true);
        foreach ($contentCreateStruct->contentType->getFieldDefinitions() as $fieldDefinition) {
            if (!isset($payload[$fieldDefinition->identifier])) {
                continue;
            }
            $fieldData = $payload[$fieldDefinition->identifier];

            $fieldType = $this->getFieldType($fieldDefinition->fieldTypeIdentifier);
            $value = $fieldType->getEmptyValue();

            $this->applyPreProcessor($fieldData, $fieldDefinition->fieldTypeIdentifier);
            foreach ($fieldData as $propertyName => $propertyValue) {
                $value->$propertyName = $propertyValue;
            }
            $contentCreateStruct->setField($fieldDefinition->identifier, $value);
        }

        try {
            $draft = $this->getContentService()->createContent(
                $contentCreateStruct,
                [$this->getLocationService()->newLocationCreateStruct($parentLocationId)]
            );

            $content = $this->getContentService()->publishVersion(
                $draft->versionInfo
            );
        // } catch (ContentValidationException $e) {
        // } catch (ContentFieldValidationException $e) {
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage(), 'trace' => $e->getTrace()],
                500
            );
        }

        $redirectUrl = $this->container->get('router')->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            ['contentId' => $content->id],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return new RedirectResponse($redirectUrl);
    }

    /**
     * @return \eZ\Publish\API\Repository\ContentTypeService
     */
    protected function getContentTypeService()
    {
        return $this->container->get('ezpublish.api.service.content_type');
    }

    /**
     * @return \eZ\Publish\API\Repository\LocationService
     */
    protected function getLocationService()
    {
        return $this->container->get('ezpublish.api.service.location');
    }

    /**
     * @return \eZ\Publish\API\Repository\ContentService
     */
    protected function getContentService()
    {
        return $this->container->get('ezpublish.api.service.content');
    }

    /**
     * @param $fieldTypeIdentifier
     *
     * @return \eZ\Publish\API\Repository\FieldType
     */
    protected function getFieldType($fieldTypeIdentifier)
    {
        static $fieldTypeService;

        if (!isset($fieldTypeService)) {
            $fieldTypeService = $this->container->get('ezpublish.api.service.field_type');
        }

        return $fieldTypeService->getFieldType($fieldTypeIdentifier);
    }

    private function configureResolver()
    {
        $resolver = $this->container->get('liform.resolver');
        $resolver->setTransformer('hidden', $this->container->get('liform.transformer.null'));
        $resolver->setTransformer('submit', $this->container->get('liform.transformer.null'));
    }

    /**
     * Applies the FieldValue pre-processor, if there is one.
     *
     * @param array $fieldValueHash
     * @param string $fieldTypeIdentifier
     */
    private function applyPreProcessor(&$fieldValueHash, $fieldTypeIdentifier)
    {
        // This one doesn't expect a hash, skip it
        if ($fieldTypeIdentifier === 'ezstring') {
            return;
        }
        $registry = $this->container->get('ezpublish_rest.field_type_processor_registry');
        if (!$registry->hasProcessor($fieldTypeIdentifier)) {
            return;
        }

        $processor = $registry->getProcessor($fieldTypeIdentifier);
        $fieldValueHash = $processor->preProcessValueHash($fieldValueHash);
    }
}
