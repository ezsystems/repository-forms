<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Form\Processor;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupCreateData;
use EzSystems\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupUpdateData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use EzSystems\RepositoryForms\Form\Processor\ContentTypeGroupFormProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

class ContentTypeGroupFormProcessorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contentTypeService;

    /**
     * @var ContentTypeGroupFormProcessor
     */
    private $processor;

    protected function setUp()
    {
        parent::setUp();
        $this->contentTypeService = $this->createMock(ContentTypeService::class);
        $this->processor = new ContentTypeGroupFormProcessor($this->contentTypeService);
    }

    public function testGetSubscribedEvents()
    {
        self::assertSame(
            [
                RepositoryFormEvents::CONTENT_TYPE_GROUP_UPDATE => ['processUpdate', 10],
            ],
            ContentTypeGroupFormProcessor::getSubscribedEvents()
        );
    }

    public function testProcessCreate()
    {
        $data = new ContentTypeGroupCreateData();
        $newContentTypeGroup = new ContentTypeGroup();
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $this->contentTypeService
            ->expects($this->once())
            ->method('createContentTypeGroup')
            ->with($data)
            ->willReturn($newContentTypeGroup);

        $this->processor->processUpdate($event);
        self::assertSame($newContentTypeGroup, $data->contentTypeGroup);
    }

    public function testProcessUpdate()
    {
        $contentTypeGroupId = 123;
        $existingContentTypeGroup = new ContentTypeGroup(['id' => $contentTypeGroupId]);
        $updatedContentTypeGroup = new ContentTypeGroup(['id' => $contentTypeGroupId]);
        $data = new ContentTypeGroupUpdateData(['contentTypeGroup' => $existingContentTypeGroup]);
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $this->contentTypeService
            ->expects($this->once())
            ->method('updateContentTypeGroup')
            ->with($existingContentTypeGroup, $data);
        $this->contentTypeService
            ->expects($this->once())
            ->method('loadContentTypeGroup')
            ->with($contentTypeGroupId)
            ->willReturn($updatedContentTypeGroup);

        $this->processor->processUpdate($event);
        self::assertSame($updatedContentTypeGroup, $data->contentTypeGroup);
    }
}
