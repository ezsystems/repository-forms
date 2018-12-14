<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;

final class ContentEditContext extends MinkContext implements Context, SnippetAcceptingContext
{
    /**
     * Name of the content that was created using the edit form. Used to validate that the content was created.
     * @var string
     */
    private $createdContentName;

    /**
     * @var \EzSystems\RepositoryForms\Behat\Context\ContentTypeContext
     */
    private $contentTypeContext;

    /**
     * Identifier of the FieldDefinition used to cover validation.
     * @var string
     */
    private static $constrainedFieldIdentifier = 'constrained_field';

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->contentTypeContext = $environment->getContext('EzSystems\RepositoryForms\Behat\Context\ContentTypeContext');
    }

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
            throw new \Exception('No created content name set');
        }

        $this->assertElementOnPage('span.ezstring-field');
        $this->assertElementContainsText('span.ezstring-field', $this->createdContentName);
    }

    /**
     * @When /^I fill in the folder edit form$/
     */
    public function iFillInTheFolderEditForm()
    {
        // will only work for single value fields
        $this->createdContentName = 'Behat content edit @' . microtime(true);
        $this->fillField('ezrepoforms_content_edit_fieldsData_name_value', $this->createdContentName);
    }

    /**
     * @Given /^that I have permission to create folders$/
     */
    public function thatIHavePermissionToCreateFolders()
    {
        $this->visit('/login');
        $this->fillField('_username', 'admin');
        $this->fillField('_password', 'publish');
        $this->getSession()->getPage()->find('css', 'form')->submit();
    }

    /**
     * @Given /^that I have permission to create content of this type$/
     */
    public function thatIHavePermissionToCreateContentOfThisType()
    {
        $this->thatIHavePermissionToCreateFolders();
    }

    /**
     * @When /^I go to the content creation page for this type$/
     */
    public function iGoToTheContentCreationPageForThisType()
    {
        $uri = sprintf(
            '/content/create/nodraft/%s/eng-GB/2',
            $this->contentTypeContext->getCurrentContentType()->identifier
        );

        $this->visit($uri);
    }

    /**
     * @Given /^I fill in the constrained field with an invalid value$/
     */
    public function iFillInTheConstrainedFieldWithAnInvalidValue()
    {
        $this->fillField(
            sprintf(
                'ezrepoforms_content_edit_fieldsData_%s_value',
                self::$constrainedFieldIdentifier
            ),
            'abc'
        );
    }

    /**
     * @Then /^I am shown the content creation form$/
     */
    public function iAmShownTheContentCreationForm()
    {
        $uri = sprintf(
            '/content/create/nodraft/%s/eng-GB/2',
            $this->contentTypeContext->getCurrentContentType()->identifier
        );

        $this->assertPageAddress($uri);
        $this->assertElementOnPage(
            sprintf(
                'input[name="ezrepoforms_content_edit[fieldsData][%s][value]"]',
                self::$constrainedFieldIdentifier
            )
        );
    }

    /**
     * @Given /^there is a relevant error message linked to the invalid field$/
     */
    public function thereIsARelevantErrorMessageLinkedToTheInvalidField()
    {
        $selector = sprintf(
            '#ezrepoforms_content_edit_fieldsData_%s div ul li',
            self::$constrainedFieldIdentifier
        );

        $this->assertSession()->elementExists('css', $selector);
        $this->assertSession()->elementTextContains('css', $selector, 'The string cannot be shorter than 5 characters.');
    }

    /**
     * @Given /^that there is a Content Type with any kind of constraints on a Field Definition$/
     */
    public function thereIsAContentTypeWithAnyKindOfConstraintsOnAFieldDefinition()
    {
        $contentTypeCreateStruct = $this->contentTypeContext->newContentTypeCreateStruct();

        $contentTypeCreateStruct->addFieldDefinition(
            new FieldDefinitionCreateStruct(
                [
                    'identifier' => self::$constrainedFieldIdentifier,
                    'fieldTypeIdentifier' => 'ezstring',
                    'names' => ['eng-GB' => 'Field'],
                    'validatorConfiguration' => [
                        'StringLengthValidator' => ['minStringLength' => 5, 'maxStringLength' => 10],
                    ],
                ]
            )
        );

        $this->contentTypeContext->createContentType($contentTypeCreateStruct);
    }

    /**
     * @When /^a content creation form is displayed$/
     */
    public function aContentCreationFormIsDisplayed()
    {
        $this->visit('/content/create/nodraft/folder/eng-GB/2');
    }
}
