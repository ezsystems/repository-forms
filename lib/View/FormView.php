<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\View;

use Symfony\Component\Form\FormView as SymfonyFormView;

/**
 * A View of a Sf Form Component FormView object.
 */
interface FormView
{
    /**
     * @return \Symfony\Component\Form\FormView|null
     */
    public function getFormView();

    /**
     * @param \Symfony\Component\Form\FormView $formView
     *
     * @return FormView
     */
    public function setFormView(SymfonyFormView $formView);
}
