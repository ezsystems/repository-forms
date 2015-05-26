<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\Form\ActionDispatcher;

use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeDispatcher implements ActionDispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatchFormAction(FormInterface $form, ValueObject $data, $actionName, array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $options = $resolver->resolve($options);

        // First dispatch default action, then $actionName.
        $event = new FormActionEvent($form, $data, $actionName, $options);
        $defaultActionEventName = RepositoryFormEvents::CONTENT_TYPE_UPDATE;
        $this->eventDispatcher->dispatch($defaultActionEventName, $event);
        $actionEventName = $defaultActionEventName . ($actionName ? ".$actionName" : '');
        $this->eventDispatcher->dispatch($actionEventName, $event);
        $this->response = $event->getResponse();
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('languageCode');
    }

    public function getResponse()
    {
        return $this->response;
    }
}
