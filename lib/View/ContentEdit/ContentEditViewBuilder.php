<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\View\ContentEdit;

use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\View;
use Symfony\Component\Form\FormInterface;

class ContentEditViewBuilder implements ViewBuilder
{
    /**
     * Tests if the builder matches the given argument.
     *
     * @param mixed $argument Anything the builder can decide against. Example: a controller's request string.
     *
     * @return bool true if the ViewBuilder matches the argument, false otherwise.
     */
    public function matches($argument)
    {
        return $argument === 'ez_content_edit:editAction';
    }

    /**
     * Builds the View based on $parameters.
     *
     * @param array $parameters
     *
     * @return View An implementation of the View interface
     */
    public function buildView(array $parameters)
    {
        if (!isset($parameters['form']) || !$parameters['form'] instanceof FormInterface) {
            throw new \InvalidArgumentException("Missing or invalid 'form' view parameter");
        }

        /** @var FormInterface $form */
        $form = $parameters['form'];

        if ($form->isValid()) {
            // @lolautruche: is there an else ?
        }

        $view = new ContentEditView();
        $view->setFormView($form->createView());
        $view->setLanguage($parameters['language']);

        return $view;
//        return $this->render('EzSystemsRepositoryFormsBundle:Content:content_create_no_draft.html.twig', [
//            'form' => $form->createView(),
//            'languageCode' => $language,
//        ]);
    }
}
