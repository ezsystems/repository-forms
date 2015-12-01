<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\View\ContentEdit;

use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use eZ\Publish\Core\MVC\Symfony\View\View;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

class ContentEditSuccessView extends BaseView implements View/*, FormView */
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    private $form;

    /**
     * The Form object that was successfully processed.
     * Could have been a FormInterface, but we use getClickedButton() on the view, and it is not part of the interface.
     *
     * @param Form $form A form object.
     *
     * @return ContentEditView
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }
}
