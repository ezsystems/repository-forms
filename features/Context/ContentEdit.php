<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;

class ContentEdit extends MinkContext implements Context, SnippetAcceptingContext
{
    /**
     * @Then /^I should see a content edit form$/
     */
    public function iShouldSeeAContentEditForm()
    {
        $this->assertSession()->elementExists('css', 'form[name=ezrepoforms_content_edit]');
    }
}
