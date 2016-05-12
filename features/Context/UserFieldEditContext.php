<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Features\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkContext;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionUpdateStruct;
use PHPUnit_Framework_Assert as Assertion;

final class UserFieldEditContext extends MinkContext implements SnippetAcceptingContext
{
    private static $fieldIdentifier = 'field';

    /** @var \EzSystems\RepositoryForms\Features\Context\ContentType */
    private $contentTypeContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->contentTypeContext = $environment->getContext('EzSystems\RepositoryForms\Features\Context\ContentType');
    }

    /**
     * @Given a Content Type with a user field definition
     */
    public function aContentTypeWithAUserFieldDefinition()
    {
        $contentTypeCreateStruct = $this->contentTypeContext->newContentTypeCreateStruct();
        $contentTypeCreateStruct->addFieldDefinition(
            new FieldDefinitionCreateStruct(
                [
                    'identifier' => self::$fieldIdentifier,
                    'fieldTypeIdentifier' => 'ezuser',
                    'names' => ['eng-GB' => 'Field'],
                ]
            )
        );
        $this->contentTypeContext->createContentType($contentTypeCreateStruct);
    }

    /**
     * @When /^I view the edit form for this field$/
     */
    public function iEditOrCreateContentOfThisType()
    {
        $this->visit(
            sprintf(
                '/content/create/nodraft/%s/eng-GB/2',
                $this->contentTypeContext->getCurrentContentType()->identifier
            )
        );
    }

    /**
     * @Then /^the edit form should contain a fieldset named after the user field definition$/
     */
    public function theEditFormShouldContainAFieldsetNamedAfterTheUserFieldDefinition()
    {
        $this->assertElementContainsText(
            sprintf('div.ezfield-identifier-%s fieldset legend', self::$fieldIdentifier),
            'Field'
        );
    }

    /**
     * @Given /^it should contain the following set of labels and input fields types:$/
     */
    public function itShouldContainTheFollowingSetOfLabelsAndInputFieldsTypes(TableNode $table)
    {
        $fieldsExpectations = $table->getColumnsHash();

        $inputNodeElements = $this->getSession()->getPage()->findAll(
            'css',
            sprintf('div.ezfield-identifier-%s fieldset input', self::$fieldIdentifier)
        );

        /** @var NodeElement $nodeElement */
        foreach ($inputNodeElements as $nodeElement) {
            foreach ($fieldsExpectations as $expectationId => $fieldExpectation) {
                if ($fieldExpectation['type'] === $nodeElement->getAttribute('type')) {
                    $inputId = $nodeElement->getAttribute('id');

                    $this->assertElementOnPage("label[for=$inputId]");
                    $this->assertElementContainsText("label[for=$inputId]", $fieldExpectation['label']);

                    unset($fieldsExpectations[$expectationId]);
                    reset($fieldsExpectations);
                    break;
                }
            }
        }
    }

    /**
     * @Given /^the user field is required$/
     */
    public function theUserFieldIsRequired()
    {
        $this->contentTypeContext->updateFieldDefinition(
            self::$fieldIdentifier,
            new FieldDefinitionUpdateStruct(['isRequired' => true])
        );
    }

    /**
     * @Then /^the user input fields should be flagged as required$/
     */
    public function theUserInputFieldsShouldBeFlaggedAsRequired()
    {
        $inputNodeElements = $this->getSession()->getPage()->findAll(
            'css',
            sprintf('div.ezfield-identifier-%s fieldset input', self::$fieldIdentifier)
        );

        foreach ($inputNodeElements as $inputNodeElement) {
            Assertion::assertEquals(
                'required',
                $inputNodeElement->getAttribute('required'),
                sprintf(
                    '%s input with id %s is not flagged as required',
                    $inputNodeElement->getAttribute('type'),
                    $inputNodeElement->getAttribute('id')
                )
            );
        }
    }
}
