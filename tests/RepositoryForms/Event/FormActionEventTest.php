<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Tests\Event;

use EzSystems\RepositoryForms\Event\FormActionEvent;
use PHPUnit_Framework_TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class FormActionEventTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $form = $this->getMock('\Symfony\Component\Form\FormInterface');
        $data = new stdClass();
        $clickedButton = 'fooButton';
        $options = ['languageCode' => 'eng-GB', 'foo' => 'bar'];

        $event = new FormActionEvent($form, $data, $clickedButton, $options);
        self::assertSame($form, $event->getForm());
        self::assertSame($data, $event->getData());
        self::assertSame($clickedButton, $event->getClickedButton());
        self::assertSame($options, $event->getOptions());
    }

    public function testEventDoesntHaveResponse()
    {
        $event = new FormActionEvent(
            $this->getMock('\Symfony\Component\Form\FormInterface'),
            new stdClass(), 'fooButton'
        );
        self::assertFalse($event->hasResponse());
        self::assertNull($event->getResponse());
    }

    public function testEventSetResponse()
    {
        $event = new FormActionEvent(
            $this->getMock('\Symfony\Component\Form\FormInterface'),
            new stdClass(), 'fooButton'
        );
        self::assertFalse($event->hasResponse());
        self::assertNull($event->getResponse());

        $response = new Response();
        $event->setResponse($response);
        self::assertTrue($event->hasResponse());
        self::assertSame($response, $event->getResponse());
    }

    public function testGetOption()
    {
        $objectOption = new stdClass();
        $options = ['languageCode' => 'eng-GB', 'foo' => 'bar', 'obj' => $objectOption];

        $event = new FormActionEvent(
            $this->getMock('\Symfony\Component\Form\FormInterface'),
            new stdClass(), 'fooButton', $options
        );
        self::assertTrue($event->hasOption('languageCode'));
        self::assertTrue($event->hasOption('foo'));
        self::assertTrue($event->hasOption('obj'));
        self::assertSame('eng-GB', $event->getOption('languageCode'));
        self::assertSame('bar', $event->getOption('foo'));
        self::assertSame($objectOption, $event->getOption('obj'));
    }
}
