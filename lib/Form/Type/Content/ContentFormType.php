<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\RepositoryForms\Form\Type\Content;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Base\Exceptions\NotFound\FieldTypeNotFoundException;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct as APIContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\Field;
use EzSystems\RepositoryForms\Form\Type\FieldValue\CheckboxFieldType;
use EzSystems\RepositoryForms\Form\Type\FieldValue\ImageFieldType;
use EzSystems\RepositoryForms\Form\Type\FieldValue\MapLocationType;
use EzSystems\RepositoryForms\Form\Type\FieldValue\TextFieldType;
use Overblog\GraphQLBundle\__DEFINITIONS__\ImageFieldValueType;
use Overblog\GraphQLBundle\__DEFINITIONS__\ISBNFieldValueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
                function(APIContentCreateStruct $struct) {
                    return $struct;
                },
                function(APIContentCreateStruct $struct) {
                    $fixUpRequired = false;

                    foreach (array_keys($struct->fields) as $key) {
                        if (!is_numeric($key)) {
                            $fixUpRequired = true;
                        }
                    }

                    if (!$fixUpRequired) {
                        return $struct;
                    }

                    $expectedFieldDefinitions = array_filter(
                        $struct->contentType->getFieldDefinitions(),
                        function (FieldDefinition $fieldDefinition) {
                            try {
                                $this->getFormTypeClass($fieldDefinition->fieldTypeIdentifier);
                            } catch (FieldTypeNotFoundException $e) {
                                // We ignore fieldtypes that are  not found
                                return false;
                            }
                            return true;
                        },
                        ARRAY_FILTER_USE_BOTH
                    );

                    if (count($struct->fields) != count($expectedFieldDefinitions)) {
                        throw new TransformationFailedException("Fields count mismatch");
                    }

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

        if (!$options['controls_enabled']) {
            return;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addControls']);

        if (!$options['drafts_enabled']) {
            return;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addDraftControls']);
    }

    public function addFields(FormEvent $event)
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\ContentCreateStruct $contentCreateStruct */
        $contentCreateStruct = $event->getData();
        $form = $event->getForm();

        foreach ($contentCreateStruct->contentType->getFieldDefinitions() as $fieldDefinition) {
            $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);

            unset($field);
            foreach ($contentCreateStruct->fields as $field) {
                if ($field->fieldDefIdentifier === $fieldDefinition->identifier) {
                    continue;
                }
            }

            if (!isset($field)) {
                $field = new Field([
                    'fieldDefIdentifier' => $fieldDefinition->identifier,
                    'value' => $fieldType->getEmptyValue(),
                    'languageCode' => 'eng-GB',
                ]);
            }

            try {
                $form->add(
                    $fieldDefinition->identifier,
                    $this->getFormTypeClass($fieldDefinition->fieldTypeIdentifier),
                    [
                        'property_path' => "fields[{$fieldDefinition->identifier}]",
                        'required' => $fieldDefinition->isRequired,
                        'label' => $fieldDefinition->getName('eng-GB'),
                        'data' => $field->value,
                        'data_class' => get_class($field->value),
                    ]
                );
            } catch (FieldTypeNotFoundException $e) {
                continue;
            }
        }
    }

    private function getFormTypeClass($fieldTypeIdentifier)
    {
        $map = [
            // 'ezauthor' => AuthorFieldType::class,
            // 'ezbinaryfile' => BinaryFileFieldType::class,
            'ezboolean' => CheckboxFieldType::class,
            'ezdate' => DateType::class,
            'ezdatetime' => DateTimeType::class,
            'ezfloat' => NumberType::class,
            'ezgmaplocation' => MapLocationType::class,
            'ezimage' => ImageFieldType::class,
            'ezisbn' => TextType::class,
            // 'ezmedia' => MediaFieldType::class,
            'ezinteger' => NumberType::class,
            // 'ezprice' => PriceFieldType::class,
            // 'ezobjectrelation' => RelationFieldType::class,
            // 'ezobjectrelations' => RelationListFieldType::class,
            // 'ezrichtext' => RichTextFieldType::class,
            // 'ezselection' => SelectionFieldType::class,
            'ezstring' => TextType::class,
            'eztext' => TextType::class,
            'eztime' => TimeType::class,
            // 'ezurl' => UrlType::class,
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
                'controls_enabled' => true,
                'drafts_enabled' => false,
                'data_class' => '\eZ\Publish\API\Repository\Values\Content\ContentCreateStruct',
                'translation_domain' => 'ezrepoforms_content',
            ])
            ->setRequired(['languageCode']);
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
