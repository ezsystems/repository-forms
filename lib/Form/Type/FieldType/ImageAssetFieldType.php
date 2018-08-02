<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\Form\Type\FieldType;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\Core\FieldType\ImageAsset\Mapper;
use EzSystems\RepositoryForms\ConfigResolver\MaxUploadSize;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageAssetFieldType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\Core\FieldType\ImageAsset\Mapper */
    private $assetMapper;

    /** @var \EzSystems\RepositoryForms\ConfigResolver\MaxUploadSize */
    private $maxUploadSize;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\Core\FieldType\ImageAsset\Mapper $mapper
     * @param \EzSystems\RepositoryForms\ConfigResolver\MaxUploadSize $maxUploadSize
     */
    public function __construct(ContentService $contentService, Mapper $mapper, MaxUploadSize $maxUploadSize)
    {
        $this->contentService = $contentService;
        $this->maxUploadSize = $maxUploadSize;
        $this->assetMapper = $mapper;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezimageasset';
    }

    public function getParent()
    {
        return BinaryBaseFieldType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('destinationContentId', HiddenType::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['image'] = null;

        if ($view->vars['value']['destinationContentId']) {
            try {
                $content = $this->contentService->loadContent(
                    $view->vars['value']['destinationContentId']
                );

                $view->vars['image'] = $this->assetMapper->getAssetValue($content);
            } catch (NotFoundException | UnauthorizedException $exception) {
            }
        }

        $view->vars['max_file_size'] = $this->getMaxFileSize();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'ezrepoforms_fieldtype',
        ]);
    }

    /**
     * Returns max size of uploaded file in bytes.
     *
     * @return int
     */
    private function getMaxFileSize(): int
    {
        $validatorConfiguration = $this->assetMapper
            ->getAssetFieldDefinition()
            ->getValidatorConfiguration();

        $maxFileSize = $validatorConfiguration['FileSizeValidator']['maxFileSize'];
        if ($maxFileSize > 0) {
            return min($maxFileSize * 1024 * 1024, $this->maxUploadSize->get());
        }

        return $this->maxUploadSize->get();
    }
}
