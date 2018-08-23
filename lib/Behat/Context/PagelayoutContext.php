<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Behat\Helper\EzEnvironmentConstants;
use EzSystems\EzPlatformAdminUi\Behat\Helper\InstallType;
use PHPUnit\Framework\Assert as Assertion;

class PagelayoutContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    /** @var string Regex matching the way the Twig template name is inserted in debug mode */
    const TWIG_DEBUG_STOP_REGEX = '<!-- STOP .*%s.* -->';

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
        Assertion::assertTrue($this->configResolver->hasParameter('pagelayout'));
    }

    /**
     * @Then /^it is rendered using the configured pagelayout$/
     */
    public function itIsRenderedUsingTheConfiguredPagelayout()
    {
        $pageLayout = $this->getPageLayout();

        $searchedPattern = sprintf(self::TWIG_DEBUG_STOP_REGEX, preg_quote($pageLayout, null));
        Assertion::assertRegExp($searchedPattern, $this->getSession()->getPage()->getOuterHtml());
    }

    public function getPageLayout(): string
    {
        $installType = EzEnvironmentConstants::getInstallType();
        switch ($installType) {
            case InstallType::PLATFORM:
            case InstallType::ENTERPRISE:
                return $this->configResolver->getParameter('pagelayout', null, 'site');
            case InstallType::PLATFORM_DEMO:
            case InstallType::ENTERPRISE_DEMO:
                return str_replace('@ezdesign', 'app/Resources/views/themes/tastefulplanet', $this->configResolver->getParameter('pagelayout', null, 'site'));
            default:
                throw new \Exception('Unrecognised install type');
        }
    }
}
