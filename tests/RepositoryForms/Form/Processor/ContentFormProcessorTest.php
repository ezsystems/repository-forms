<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\Tests\Form\Processor;

use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use EzSystems\RepositoryForms\Data\Content\ContentUpdateData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use EzSystems\RepositoryForms\Form\Processor\ContentFormProcessor;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\Routing\RouterInterface;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Field;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;

class ContentFormProcessorTest extends TestCase
{
    private const LANGUAGE_CODE = 'cyb-CY';
    private const CONTENT_ID = 123;
    private const FIELD_1_IDENTIFIER = 'field_1_identifier';
    private const FIELD_2_IDENTIFIER = 'field_2_identifier';

    /** @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \EzSystems\RepositoryForms\Form\Processor\ContentFormProcessor */
    private $processor;

    /** @var \eZ\Publish\Core\Repository\Values\Content\VersionInfo */
    private $versionInfo;

    /** @var \eZ\Publish\Core\Repository\Values\Content\Content */
    private $contentDraft;

    protected function setUp()
    {
        parent::setUp();
        $this->contentService = $this->createMock(ContentService::class);
        $this->locationService = $this->createMock(LocationService::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->processor = new ContentFormProcessor($this->contentService, $this->locationService, $this->router);
        $this->versionInfo = new VersionInfo([
            'contentInfo' => new ContentInfo([
                'id' => self::CONTENT_ID,
                'mainLanguageCode' => self::LANGUAGE_CODE,
            ]),
        ]);
        $this->contentDraft = new Content([
            'versionInfo' => $this->versionInfo,
        ]);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [
                RepositoryFormEvents::CONTENT_PUBLISH => ['processPublish', 10],
                RepositoryFormEvents::CONTENT_CANCEL => ['processCancel', 10],
                RepositoryFormEvents::CONTENT_SAVE_DRAFT => ['processSaveDraft', 10],
                RepositoryFormEvents::CONTENT_CREATE_DRAFT => ['processCreateDraft', 10],
            ],
            ContentFormProcessor::getSubscribedEvents()
        );
    }

    public function testProcessSaveDraftWithTwoUpdatedFields(): void
    {
        $form = $this->getSaveDraftForm();

        $field1 = $this->getField(self::FIELD_1_IDENTIFIER, self::LANGUAGE_CODE, 'field_1_value');
        $field2 = $this->getField(self::FIELD_2_IDENTIFIER, self::LANGUAGE_CODE, 'field_2_value');

        $data = new ContentUpdateData([
            'contentDraft' => $this->contentDraft,
            'fields' => [],
            'fieldsData' => [
                $field1->fieldDefIdentifier => new FieldData([
                    'field' => $field1,
                    'fieldDefinition' => $this->getFieldDefinition($field1->fieldDefIdentifier),
                    'value' => 'new_field_1_value',
                ]),
                $field2->fieldDefIdentifier => new FieldData([
                    'field' => $field2,
                    'fieldDefinition' => $this->getFieldDefinition($field2->fieldDefIdentifier),
                    'value' => 'new_field_2_value',
                ]),
            ],
        ]);

        $updateContentData = new ContentUpdateData([
            'contentDraft' => $this->contentDraft,
            'fields' => [
                $this->getField(self::FIELD_1_IDENTIFIER, self::LANGUAGE_CODE, 'new_field_1_value'),
                $this->getField(self::FIELD_1_IDENTIFIER, self::LANGUAGE_CODE, 'new_field_2_value'),
            ],
            'fieldsData' => [
                $field1->fieldDefIdentifier => new FieldData([
                    'field' => $field1,
                    'fieldDefinition' => $this->getFieldDefinition($field1->fieldDefIdentifier),
                    'value' => 'new_field_1_value',
                ]),
                $field2->fieldDefIdentifier => new FieldData([
                    'field' => $field2,
                    'fieldDefinition' => $this->getFieldDefinition($field2->fieldDefIdentifier),
                    'value' => 'new_field_2_value',
                ]),
            ],
        ]);

        $event = new FormActionEvent($form, $data, 'foo');

        $this->contentService
            ->expects($this->once())
            ->method('updateContent')
            ->withConsecutive($this->versionInfo, $updateContentData)
            ->willReturn($this->contentDraft);

        $this->processor->processSaveDraft($event);
    }

    public function testProcessPublishWithOneUpdatedField(): void
    {
        $form = $this->getPublishForm();

        $field1 = $this->getField(self::FIELD_1_IDENTIFIER, self::LANGUAGE_CODE, 'field_1_value');
        $field2 = $this->getField(self::FIELD_2_IDENTIFIER, self::LANGUAGE_CODE, 'field_2_value');

        $data = new ContentUpdateData([
            'contentDraft' => $this->contentDraft,
            'fields' => [],
            'fieldsData' => [
                $field1->fieldDefIdentifier => new FieldData([
                    'field' => $field1,
                    'fieldDefinition' => $this->getFieldDefinition($field1->fieldDefIdentifier),
                    'value' => 'new_field_1_value',
                ]),
                $field2->fieldDefIdentifier => new FieldData([
                    'field' => $field2,
                    'fieldDefinition' => $this->getFieldDefinition($field2->fieldDefIdentifier),
                    'value' => 'field_2_value',
                ]),
            ],
        ]);

        $updateContentData = new ContentUpdateData([
            'contentDraft' => $this->contentDraft,
            'fields' => [$this->getField(self::FIELD_1_IDENTIFIER, self::LANGUAGE_CODE, 'new_field_1_value')],
            'fieldsData' => [
                $field1->fieldDefIdentifier => new FieldData([
                    'field' => $field1,
                    'fieldDefinition' => $this->getFieldDefinition($field1->fieldDefIdentifier),
                    'value' => 'new_field_1_value',
                ]),
                $field2->fieldDefIdentifier => new FieldData([
                    'field' => $field2,
                    'fieldDefinition' => $this->getFieldDefinition($field2->fieldDefIdentifier),
                    'value' => 'field_2_value',
                ]),
            ],
        ]);

        $event = new FormActionEvent($form, $data, 'foo');

        $this->contentService
            ->expects($this->once())
            ->method('updateContent')
            ->with($this->versionInfo, $updateContentData)
            ->willReturn($this->contentDraft);

        $this->processor->processPublish($event);
    }

    /**
     * @param string $fieldDefIdentifier
     * @param string $languageCode
     * @param string $value
     *
     * @return Field
     */
    private function getField($fieldDefIdentifier = 'identifier', $languageCode = self::LANGUAGE_CODE, $value = 'string_value'): Field
    {
        return new Field([
            'fieldDefIdentifier' => $fieldDefIdentifier,
            'languageCode' => $languageCode,
            'value' => $value,
        ]);
    }

    /**
     * @param string $identifier
     *
     * @return FieldDefinition
     */
    private function getFieldDefinition(string $identifier = 'identifier'): FieldDefinition
    {
        return new FieldDefinition([
            'identifier' => $identifier,
            'defaultValue' => 'default_value',
        ]);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Form\FormInterface
     */
    private function getPublishForm()
    {
        $formConfigInterface = $this->createMock(FormConfigInterface::class);
        $formConfigInterface
            ->method('getOption')
            ->with('languageCode')
            ->willReturn(self::LANGUAGE_CODE);

        $formRedirectUrlAfterPublish = $this->createMock(FormInterface::class);
        $formRedirectUrlAfterPublish
            ->method('getData')
            ->willReturn('url');

        $form = $this->createMock(FormInterface::class);
        $form
            ->method('getConfig')
            ->willReturn($formConfigInterface);
        $form
            ->method('offsetGet')
            ->with('redirectUrlAfterPublish')
            ->willReturn($formRedirectUrlAfterPublish);

        return $form;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Form\FormInterface
     */
    private function getSaveDraftForm()
    {
        $formConfigInterface = $this->createMock(FormConfigInterface::class);
        $formConfigInterface
            ->method('getOption')
            ->with('languageCode')
            ->willReturn(self::LANGUAGE_CODE);

        $formConfigInterface
            ->method('getAction')
            ->willReturn('action-url');

        $form = $this->createMock(FormInterface::class);
        $form
            ->method('getConfig')
            ->willReturn($formConfigInterface);

        return $form;
    }
}
