<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\FieldType;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\FieldType\RichText\Converter;
use EzSystems\RepositoryForms\FieldType\DataTransformer\RichTextValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form Type representing ezrichtext field type.
 */
class RichTextFieldType extends AbstractType
{
    /** @var FieldTypeService */
    protected $fieldTypeService;

    /** @var Converter */
    protected $docbookToXhtml5EditConverter;

    public function __construct(FieldTypeService $fieldTypeService, Converter $docbookToXhtml5EditConverter)
    {
        $this->fieldTypeService = $fieldTypeService;
        $this->docbookToXhtml5EditConverter = $docbookToXhtml5EditConverter;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezrichtext';
    }

    public function getParent()
    {
        return TextareaType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new RichTextValueTransformer(
            $this->fieldTypeService->getFieldType('ezrichtext'),
            $this->docbookToXhtml5EditConverter
        ));
    }
}
