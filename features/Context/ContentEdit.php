<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\MinkContext;
use PHPUnit_Framework_Assert as Assertion;

class ContentEdit extends MinkContext implements Context, SnippetAcceptingContext
{
    /**
     * The
     */
    private $createdContentName;

    /**
     * @Then /^I should see a folder content edit form$/
     * @Then /^I should see a content edit form$/
     */
    public function iShouldSeeAContentEditForm()
    {
        $this->assertSession()->elementExists('css', 'form[name=ezrepoforms_content_edit]');
    }

    /**
     * @Then /^I am on the View of the Content that was published$/
     */
    public function iAmOnTheViewOfTheContentThatWasPublished()
    {
        if (!isset($this->createdContentName)) {
            throw new \Exception("No created content name set");
        }

        $page = $this->getSession()->getPage();
        Assertion::assertTrue($page->has('css', 'span.ezstring-field'));
        Assertion::assertEquals($this->createdContentName, $page->find('css', 'span.ezstring-field')->getText());
    }

    /**
     * @When /^I fill in the folder edit form$/
     */
    public function iFillInTheFolderEditForm()
    {
        // will only work for single value fields
        $this->createdContentName = "Behat content edit @" . microtime(true);
        $this->fillField("ezrepoforms_content_edit_fieldsData_name_value", $this->createdContentName);
    }

    /**
     * @Given /^that I have permission to create folders$/
     */
    public function thatIHavePermissionToCreateFolders()
    {
        $this->visit('/login');
        $this->fillField('Username', 'admin');
        $this->fillField('Password', 'publish');
        $this->pressButton('Login');
    }
}
