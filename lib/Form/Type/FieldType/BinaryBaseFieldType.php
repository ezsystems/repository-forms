<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Parent Form Type for binary file based field types.
 */
class BinaryBaseFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'remove',
                CheckboxType::class,
                [
                    'label' => /** @Desc("Remove") */ 'content.field_type.binary_base.remove',
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label' => /** @Desc("File") */ 'content.field_type.binary_base.file',
                    'required' => $options['required'],
                    'constraints' => [
                        new Assert\File([
                            'maxSize' => $this->getMaxUploadSize(),
                        ]),
                    ],
                ]
            );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['max_upload_size'] = $this->getMaxUploadSize();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['translation_domain' => 'ezrepoforms_fieldtype']);
    }

    private function getMaxUploadSize()
    {
        static $value = null;
        if ($value === null) {
            return $this->str2bytes(ini_get('upload_max_filesize'));
        }

        return $value;
    }

    private function str2bytes($str)
    {
        $str = strtoupper(trim($str));

        $value = substr($str, 0, -1);
        $unit = substr($str, -1);
        switch ($unit) {
            case 'G':
                $value *= 1024;
            case 'M':
                $value *= 1024;
            case 'K':
                $value *= 1024;
        }

        return (int) $value;
    }
}
