<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Tests\Form\Processor;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\Data\ContentTypeData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use EzSystems\RepositoryForms\Form\Processor\ContentTypeFormProcessor;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ContentTypeFormProcessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contentTypeService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $router;

    /**
     * @var ContentTypeFormProcessor
     */
    private $formProcessor;

    protected function setUp()
    {
        parent::setUp();
        $this->contentTypeService = $this->getMock('\eZ\Publish\API\Repository\ContentTypeService');
        $this->router = $this->getMock('\Symfony\Component\Routing\RouterInterface');
        $this->formProcessor = new ContentTypeFormProcessor($this->contentTypeService, $this->router);
    }

    public function testSubscribedEvents()
    {
        self::assertSame([
            RepositoryFormEvents::CONTENT_TYPE_ADD_FIELD_DEFINITION => 'processAddFieldDefinition',
            RepositoryFormEvents::CONTENT_TYPE_PUBLISH => 'processPublishContentType',
        ], ContentTypeFormProcessor::getSubscribedEvents());
    }

    public function testAddFieldDefinition()
    {
        $languageCode = 'fre-FR';
        $existingFieldDefinitions = [
            new FieldDefinition(),
            new FieldDefinition(),
        ];
        $contentTypeDraft = new ContentTypeDraft([
            'innerContentType' => new ContentType([
                'fieldDefinitions' => $existingFieldDefinitions
            ])
        ]);
        $fieldTypeIdentifier = 'ezstring';
        $expectedNewFieldDefIdentifier = sprintf(
            'new_%s_%d',
            $fieldTypeIdentifier,
            count($existingFieldDefinitions) + 1
        );

        $fieldTypeSelectionForm = $this->getMock('\Symfony\Component\Form\FormInterface');
        $fieldTypeSelectionForm
            ->expects($this->once())
            ->method('getData')
            ->willReturn($fieldTypeIdentifier);
        $mainForm = $this->getMock('\Symfony\Component\Form\FormInterface');
        $mainForm
            ->expects($this->once())
            ->method('get')
            ->with('fieldTypeSelection')
            ->willReturn($fieldTypeSelectionForm);

        $expectedFieldDefCreateStruct = new FieldDefinitionCreateStruct([
            'fieldTypeIdentifier' => $fieldTypeIdentifier,
            'identifier' => $expectedNewFieldDefIdentifier,
            'names' => [$languageCode => 'New FieldDefinition'],
        ]);
        $this->contentTypeService
            ->expects($this->once())
            ->method('addFieldDefinition')
            ->with($contentTypeDraft, $this->equalTo($expectedFieldDefCreateStruct));

        $event = new FormActionEvent(
            $mainForm,
            new ContentTypeData(['contentTypeDraft' => $contentTypeDraft]),
            'addFieldDefinition',
            $languageCode
        );
        $this->formProcessor->processAddFieldDefinition($event);
    }

    public function testPublishContentType()
    {
        $contentTypeDraft = new ContentTypeDraft();
        $event = new FormActionEvent(
            $this->getMock('\Symfony\Component\Form\FormInterface'),
            new ContentTypeData(['contentTypeDraft' => $contentTypeDraft]),
            'publishContentType', 'eng-GB'
        );
        $this->contentTypeService
            ->expects($this->once())
            ->method('publishContentTypeDraft')
            ->with($contentTypeDraft);

        $this->formProcessor->processPublishContentType($event);
    }

    public function testPublishContentTypeWithRedirection()
    {
        $redirectRoute = 'foo';
        $redirectUrl = 'http://foo.com/bar';
        $contentTypeDraft = new ContentTypeDraft();
        $event = new FormActionEvent(
            $this->getMock('\Symfony\Component\Form\FormInterface'),
            new ContentTypeData(['contentTypeDraft' => $contentTypeDraft]),
            'publishContentType', 'eng-GB'
        );
        $this->contentTypeService
            ->expects($this->once())
            ->method('publishContentTypeDraft')
            ->with($contentTypeDraft);

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with($redirectRoute)
            ->willReturn($redirectUrl);
        $expectedRedirectResponse = new RedirectResponse($redirectUrl);
        $formProcessor = new ContentTypeFormProcessor($this->contentTypeService, $this->router, ['redirectRouteAfterPublish' => $redirectRoute]);
        $formProcessor->processPublishContentType($event);
        self::assertTrue($event->hasResponse());
        self::assertEquals($expectedRedirectResponse, $event->getResponse());
    }
}
