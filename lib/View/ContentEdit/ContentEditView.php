<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\View\ContentEdit;

use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use eZ\Publish\Core\MVC\Symfony\View\View;
use Symfony\Component\Form\FormView;

class ContentEditView extends BaseView implements View/*, FormView */
{
    /**
     * @var \Symfony\Component\Form\FormView
     */
    private $formView;

    /**
     * @var string
     */
    private $language;

    /**
     * @param FormView $formView
     *
     * @return ContentEditView
     */
    public function setFormView(FormView $formView)
    {
        $this->formView = $formView;

        return $this;
    }

    /**
     * @return \Symfony\Component\Form\FormView|null
     */
    public function getFormView()
    {
        return $this->formView;
    }

    /**
     * @param mixed $language
     *
     * @return ContentEditView
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
