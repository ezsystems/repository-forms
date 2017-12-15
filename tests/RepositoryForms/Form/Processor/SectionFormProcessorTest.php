<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Tests\Form\Processor;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use EzSystems\RepositoryForms\Data\Section\SectionCreateData;
use EzSystems\RepositoryForms\Data\Section\SectionUpdateData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use EzSystems\RepositoryForms\Form\Processor\SectionFormProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

class SectionFormProcessorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\SectionService
     */
    private $sectionService;

    /**
     * @var SectionFormProcessor
     */
    private $processor;

    protected function setUp()
    {
        parent::setUp();
        $this->sectionService = $this->createMock(SectionService::class);
        $this->processor = new SectionFormProcessor($this->sectionService);
    }

    public function testGetSubscribedEvents()
    {
        self::assertSame(
            [
                RepositoryFormEvents::SECTION_UPDATE => ['processUpdate', 10],
            ],
            SectionFormProcessor::getSubscribedEvents()
        );
    }

    public function testProcessCreate()
    {
        $data = new SectionCreateData();
        $newSection = new Section();
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $this->sectionService
            ->expects($this->once())
            ->method('createSection')
            ->with($data)
            ->willReturn($newSection);

        $this->processor->processUpdate($event);
        self::assertSame($newSection, $data->section);
    }

    public function testProcessUpdate()
    {
        $existingSection = new Section();
        $updatedSection = new Section();
        $data = new SectionUpdateData(['section' => $existingSection]);
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $this->sectionService
            ->expects($this->once())
            ->method('updateSection')
            ->with($existingSection, $data)
            ->willReturn($updatedSection);

        $this->processor->processUpdate($event);
        self::assertSame($updatedSection, $data->section);
    }
}
