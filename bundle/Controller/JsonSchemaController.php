<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\Controller;

use EzSystems\RepositoryForms\Data\Mapper\ContentCreateMapper;
use EzSystems\RepositoryForms\Form\Type\Content\ContentEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @property \Symfony\Component\DependencyInjection\ContainerInterface $container
 */
class JsonSchemaController extends Controller
{
    public function createContentTypeFormAction($contentTypeIdentifier, $parentLocationId, $language = 'eng-GB')
    {
        $contentTypeService = $this->getContentTypeService();
        $locationService = $this->getLocationService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        $data = (new ContentCreateMapper())->mapToFormData($contentType, [
            'mainLanguageCode' => $language,
            'parentLocation' => $locationService->newLocationCreateStruct($parentLocationId),
        ]);
        $form = $this->createForm(
            ContentEditType::class,
            $data,
            ['languageCode' => $language, 'csrf_protection' => false, 'controls_enabled' => false]
        );

//        $builder = $this->getFormBuilder();
//        /**
//         * @var $field \Symfony\Component\Form\Form
//         */
//        foreach ( $form->get('fieldsData') as $field) {
//            echo get_class($field->getConfig()->getFormFactory()->);
//            $value = $field->get('value');
//            //$builder->add($field->);
//        }

        $this->configureResolver();

        $response = new JsonResponse(
            $this->get('liform')->transform($form),
            200,
            ['Content-Type' => 'application/schema+json']
        );
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);

        return $response;
    }

    /**
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    private function getFormBuilder()
    {
        return $this->container->get('form.factory')->createBuilder();
    }

    /**
     * @return mixed
     */
    protected function getContentTypeService()
    {
        return $this->container->get('ezpublish.api.service.content_type');
    }

    /**
     * @return mixed
     */
    protected function getLocationService()
    {
        return $this->container->get('ezpublish.api.service.location');
    }

    private function configureResolver()
    {
        $resolver = $this->container->get('liform.resolver');
        $resolver->setTransformer('hidden', $this->container->get('liform.transformer.null'));
        $resolver->setTransformer('submit', $this->container->get('liform.transformer.null'));
    }
}
