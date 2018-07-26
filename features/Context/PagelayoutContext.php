<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use PHPUnit\Framework\Assert as Assertion;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PagelayoutContext extends RawMinkContext implements Context, SnippetAcceptingContext, ContainerAwareInterface
{
    /** @var string Regex matching the way the Twig template name is inserted in debug mode */
    const TWIG_DEBUG_STOP_REGEX = '<!-- STOP .*%s.* -->';

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->configResolver = $this->container->getParameter('@ezpublish.config.resolver');
    }

    /**
     * @Given /^a pagelayout is configured$/
     */
    public function aPagelayoutIsConfigured()
    {
        Assertion::assertTrue($this->configResolver->hasParameter('pagelayout'));
    }

    /**
     * @Then /^it is rendered using the configured pagelayout$/
     */
    public function itIsRenderedUsingTheConfiguredPagelayout()
    {
        $pageLayout = $this->configResolver->getParameter('pagelayout');
        $searchedPattern = sprintf(self::TWIG_DEBUG_STOP_REGEX, preg_quote($pageLayout, null));
        Assertion::assertRegExp($searchedPattern, $this->getSession()->getPage()->getOuterHtml());
    }
}
