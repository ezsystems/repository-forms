<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\View\ContentEdit;

use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use eZ\Publish\Core\MVC\Symfony\View\View;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

class ContentEditViewBuilder implements ViewBuilder
{
    /** @var \eZ\Publish\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    public function __construct(
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector
    ) {
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
    }

    /**
     * Tests if the builder matches the given argument.
     *
     * @param mixed $argument Anything the builder can decide against. Example: a controller's request string.
     *
     * @return bool true if the ViewBuilder matches the argument, false otherwise.
     */
    public function matches($argument)
    {
        return $argument === 'ez_content_edit:editAction' || $argument === 'ez_content_edit:createWithoutDraftAction';
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

        /** @var Form $form */
        $form = $parameters['form'];

        if ($form->isValid()) {
            $view = new ContentEditSuccessView();
            $view->setForm($form);
            $view->setControllerReference(new ControllerReference('ez_content_edit:editSuccessAction'));

            return $view;
        }
        $view = new ContentEditView();
        $view->setFormView($form->createView());
        $view->setLanguage($parameters['language']);

        $this->viewConfigurator->configure($view);
        $this->viewParametersInjector->injectViewParameters($view, $parameters);

        return $view;
//        return $this->render('EzSystemsRepositoryFormsBundle:Content:content_create_no_draft.html.twig', [
//            'form' => $form->createView(),
//            'languageCode' => $language,
//        ]);
    }
}
