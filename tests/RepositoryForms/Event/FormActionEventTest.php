<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
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
        $languageCode = 'eng-GB';

        $event = new FormActionEvent($form, $data, $clickedButton, $languageCode);
        self::assertSame($form, $event->getForm());
        self::assertSame($data, $event->getData());
        self::assertSame($clickedButton, $event->getClickedButton());
        self::assertSame($languageCode, $event->getLanguageCode());
    }

    public function testEventDoesntHaveResponse()
    {
        $event = new FormActionEvent(
            $this->getMock('\Symfony\Component\Form\FormInterface'),
            new stdClass(), 'fooButton', 'eng-GB'
        );
        self::assertFalse($event->hasResponse());
        self::assertNull($event->getResponse());
    }

    public function testEventSetResponse()
    {
        $event = new FormActionEvent(
            $this->getMock('\Symfony\Component\Form\FormInterface'),
            new stdClass(), 'fooButton', 'eng-GB'
        );
        self::assertFalse($event->hasResponse());
        self::assertNull($event->getResponse());

        $response = new Response();
        $event->setResponse($response);
        self::assertTrue($event->hasResponse());
        self::assertSame($response, $event->getResponse());
    }
}
