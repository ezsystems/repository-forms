<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\View\ContentEdit;

use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use eZ\Publish\Core\MVC\Symfony\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ContentEditView extends BaseView implements View/*, FormView */
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    private $form;

    /**
     * @var string
     */
    private $language;

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

    /**
     * @param FormInterface $form
     *
     * @return ContentEditView
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}
