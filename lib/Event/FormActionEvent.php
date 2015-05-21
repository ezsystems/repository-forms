<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
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
     * Language code current form is edited in.
     *
     * @var string
     */
    private $languageCode;

    /**
     * Response to return after form post-processing. Typically a RedirectResponse.
     *
     * @var Response
     */
    private $response;

    public function __construct(FormInterface $form, $data, $clickedButton, $languageCode)
    {
        parent::__construct($form, $data);
        $this->clickedButton = $clickedButton;
        $this->languageCode = $languageCode;
    }

    /**
     * @return string
     */
    public function getClickedButton()
    {
        return $this->clickedButton;
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
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
}
