<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\View\ContentEdit;

use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use EzSystems\RepositoryForms\View\FormView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Injects the form property from FormView objects into the view parameters.
 */
class FormViewParameterInjector implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [ViewEvents::FILTER_VIEW_PARAMETERS => 'injectForm'];
    }

    public function injectForm(FilterViewParametersEvent $event)
    {
        if (!($view = $event->getView()) instanceof FormView) {
            return;
        }

        $event->getParameterBag()->add(['form' => $view->getFormView()]);
    }
}
