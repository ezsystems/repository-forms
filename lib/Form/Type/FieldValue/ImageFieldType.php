<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\RepositoryForms\Form\Type\FieldValue;

use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageFieldType extends AbstractFieldType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alternativeText', TextType::class, ['label' => 'Alternative text']);
        $builder->add('inputUri', FileType::class, ['label' => 'File']);

        $builder->get('inputUri')->addModelTransformer(new CallbackTransformer(
            function ($image) {
                return $image;
            },
            function ($data) {
                if (!$data instanceof UploadedFile) {
                    return $data;
                }

                $file = $data->move(sys_get_temp_dir(), $data->getClientOriginalName());

                return $file->getPathname();
            }
        ));

        $builder->addModelTransformer(
            new CallbackTransformer(
                function (ImageValue $image) {
                    return $image;
                },
                function (ImageValue $image) {
                    $image->fileName = basename($image->inputUri);

                    return $image;
                }
            )
        );
    }
}
