<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Event;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class FormActionEvent extends FormEvent
{
    /**
     * Name of the button used to submit the form.
     *
     * @var string
     */
    private $clickedButton;

    /**
     * Hash of options.
     *
     * @var array
     */
    private $options;

    /**
     * Response to return after form post-processing. Typically a RedirectResponse.
     *
     * @var Response
     */
    private $response;

    /**
     * Additional payload populated for event listeners next in priority.
     *
     * @var array
     */
    private $payloads;

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @param $data
     * @param $clickedButton
     * @param array $options
     * @param array $payloads
     */
    public function __construct(FormInterface $form, $data, $clickedButton, array $options = [], array $payloads = [])
    {
        parent::__construct($form, $data);
        $this->clickedButton = $clickedButton;
        $this->options = $options;
        $this->payloads = $payloads;
    }

    /**
     * @return string
     */
    public function getClickedButton()
    {
        return $this->clickedButton;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $optionName The option name
     * @param mixed $defaultValue Default value to return if option is not set.
     *
     * @return mixed
     */
    public function getOption($optionName, $defaultValue = null)
    {
        if (!isset($this->options[$optionName])) {
            return $defaultValue;
        }

        return $this->options[$optionName];
    }

    /**
     * @param string $optionName
     *
     * @return bool
     */
    public function hasOption($optionName)
    {
        return isset($this->options[$optionName]);
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function hasResponse()
    {
        return $this->response !== null;
    }

    /**
     * @return array
     */
    public function getPayloads(): array
    {
        return $this->payloads;
    }

    /**
     * @param array $payloads
     */
    public function setPayloads(array $payloads): void
    {
        $this->payloads = $payloads;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasPayload(string $name): bool
    {
        return isset($this->payloads[$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getPayload(string $name)
    {
        return $this->payloads[$name];
    }

    /**
     * @param string $name
     * @param mixed $payload
     */
    public function setPayload(string $name, $payload): void
    {
        $this->payloads[$name] = $payload;
    }
}
