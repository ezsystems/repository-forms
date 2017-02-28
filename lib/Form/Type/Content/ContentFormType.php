<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\Content;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Base\Exceptions\NotFound\FieldTypeNotFoundException;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct as APIContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\Field;
use EzSystems\RepositoryForms\Form\Type\FieldValue\AuthorCollectionFieldType;
use EzSystems\RepositoryForms\Form\Type\FieldValue\CheckboxFieldType;
use EzSystems\RepositoryForms\Form\Type\FieldValue\ImageFieldType;
use EzSystems\RepositoryForms\Form\Type\FieldValue\MapLocationFieldType;
use EzSystems\RepositoryForms\Form\Type\FieldValue\TextFieldType;
use EzSystems\RepositoryForms\Form\Type\FieldValue\UrlFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentFormType extends AbstractType
{
    /**
     * @var FieldTypeService
     */
    private $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_content';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addFields']);
        $builder->addModelTransformer(
            new CallbackTransformer(
                /**
                 * Converts to the provided to the one understood by the Form.
                 * Changes numerically indexed fields in ContentStruct::fields items to field def identifier.
                 *
                 * Updates the ContentCreateStruct so that fields in the struct's language code:
                 * - contain FieldValue objects instead of Field
                 * - are indexed on the FieldDefinition identifier
                 */
                function (APIContentCreateStruct $struct) {
                    $fields = $struct->fields;
                    $struct->fields = [];
                    foreach ($fields as $key => $field) {
                        $value = $field->value;
                        if (!is_object($value)) {
                            // Find the field definition's FieldType
                            foreach ($struct->contentType->getFieldDefinitions() as $fieldDefinition) {
                                if ($fieldDefinition->identifier === $field->fieldDefIdentifier) {
                                    $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);
                                }
                            }

                            if (!isset($fieldType)) {
                                throw new \Exception('Field definition identifier not found in the FieldType');
                            }
                            $valueClass = get_class($fieldType->getEmptyValue());
                            $value = new $valueClass($value);
                        }
                        $struct->fields[$field->fieldDefIdentifier] = $value;
                    }

                    return $struct;
                },
                /**
                 * Converts from the Form data back to a format understood by eZ Platform.
                 * - must set all Values to Fields.
                 */
                function (APIContentCreateStruct $struct) {
                    $inputFields = $struct->fields;
                    $struct->fields = [];

                    foreach ($struct->contentType->getFieldDefinitions() as $index => $fieldDefinition) {
                        if (isset($inputFields[$fieldDefinition->identifier])) {
                            $struct->setField($fieldDefinition->identifier, $inputFields[$fieldDefinition->identifier]);
                        }
                    }

                    return $struct;
                }
            )
        );

        if ($options['enable_remote_id']) {
            $builder->add('remoteId', TextType::class, ['label' => 'Content remote id']);
        }

        if (!$options['enable_controls']) {
            return;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addControls']);

        if (!$options['enable_drafts']) {
            return;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addDraftControls']);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        foreach ($view->children as $fieldDefIdentifier => $fieldForm) {
            if (count($fieldForm->children) === 1) {
                $view->children[$fieldDefIdentifier]->vars['label'] = false;
            }
        }
    }

    public function addFields(FormEvent $event)
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\ContentCreateStruct $contentCreateStruct */
        $contentCreateStruct = $event->getData();
        $form = $event->getForm();

        foreach ($contentCreateStruct->contentType->getFieldDefinitions() as $fieldDefinition) {
            $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);

            foreach ($contentCreateStruct->fields as $fieldIndex => $field) {
                if ($field->fieldDefIdentifier === $fieldDefinition->identifier && $field->languageCode === $contentCreateStruct->mainLanguageCode) {
                    continue;
                } else {
                    unset($field);
                }
            }

            if (!isset($field)) {
                $contentCreateStruct->setField(
                    $fieldDefinition->identifier,
                    $fieldType->getEmptyValue()
                );

                $fieldIndex = count($contentCreateStruct->fields) - 1;
                $field = $contentCreateStruct->fields[$fieldIndex];
            }

            try {
                $options = [
                    'property_path' => "fields[$fieldDefinition->identifier]",
                    'required' => $fieldDefinition->isRequired,
                    'data_class' => get_class($fieldType->getEmptyValue()),
                    'field_definition' => $fieldDefinition,
                ];

                $options['properties_constraints'] = $fieldType->getConstraints($fieldDefinition);

                $form->add(
                    $fieldDefinition->identifier,
                    $this->getFormTypeClass($fieldDefinition->fieldTypeIdentifier),
                    $options
                );
            } catch (FieldTypeNotFoundException $e) {
                continue;
            }
        }
    }

    private function getFormTypeClass($fieldTypeIdentifier)
    {
        $map = [
            'ezauthor' => AuthorCollectionFieldType::class,
            // 'ezbinaryfile' => BinaryFileFieldType::class,
            'ezboolean' => CheckboxFieldType::class,
            //'ezdate' => DateFieldType::class,
            //'ezdatetime' => DateTimeType::class,
            // 'ezfloat' => FloatFieldType::class,
            'ezgmaplocation' => MapLocationFieldType::class,
            'ezimage' => ImageFieldType::class,
            // 'ezisbn' => ISBNFieldType::class,
            // 'ezmedia' => MediaFieldType::class,
            'ezinteger' => NumberType::class,
            // 'ezprice' => PriceFieldType::class,
            // 'ezobjectrelation' => RelationFieldType::class,
            // 'ezobjectrelations' => RelationListFieldType::class,
            // 'ezrichtext' => RichTextFieldType::class,
            // 'ezselection' => SelectionFieldType::class,
            'ezstring' => TextFieldType::class,
            'eztext' => TextFieldType::class,
            'eztime' => TimeType::class,
            'ezurl' => UrlFieldType::class,
        ];

        if (isset($map[$fieldTypeIdentifier])) {
            return $map[$fieldTypeIdentifier];
        }

        throw new FieldTypeNotFoundException($fieldTypeIdentifier);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'enable_controls' => true,
                'enable_drafts' => false,
                'enable_remote_id' => false,
                'data_class' => 'eZ\Publish\API\Repository\Values\Content\ContentCreateStruct',
                'translation_domain' => 'ezrepoforms_content',

            ])
            ->setRequired(['languageCode', 'parentLocationId']);
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function addControls(FormEvent $event)
    {
        $event->getForm()
            ->add('redirectUrlAfterPublish', HiddenType::class, ['required' => false, 'mapped' => false])
            ->add('publish', SubmitType::class, ['label' => 'content.publish_button']);
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function addDraftControls(FormEvent $event)
    {
        $event->getForm()
            ->add('saveDraft', SubmitType::class, ['label' => 'content.save_button'])
            ->add('cancel', SubmitType::class, [
                'label' => 'content.cancel_button',
                'attr' => ['formnovalidate' => 'formnovalidate'],
            ]);
    }
}
