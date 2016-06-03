<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use PHPUnit_Framework_Assert as Assertion;

class PagelayoutContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @injectService $configResolver @ezpublish.config.resolver
     */
    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * @Given /^a pagelayout is configured$/
     */
    public function aPagelayoutIsConfigured()
    {
        $this->configResolver->hasParameter('pagelayout');
    }

    /**
     * @Then /^it is rendered using the configured pagelayout$/
     */
    public function itIsRenderedUsingTheConfiguredPagelayout()
    {
        Assertion::assertContains(
            sprintf('<!-- STOP %s -->', $this->configResolver->getParameter('pagelayout')),
            $this->getSession()->getPage()->getOuterHtml()
        );
    }
}
