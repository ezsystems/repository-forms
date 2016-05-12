<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Features\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\RawMinkContext;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionUpdateStruct;
use PHPUnit_Framework_Assert as Assertion;

final class FieldTypeFormContext extends RawMinkContext implements SnippetAcceptingContext
{
    private static $fieldIdentifier = 'field';

    private static $fieldTypeIdentifierMap = [
        'user' => 'ezuser',
        'textline' => 'ezstring',
        'selection' => 'ezselection',
    ];

    /** @var \EzSystems\RepositoryForms\Features\Context\ContentType */
    private $contentTypeContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->contentTypeContext = $environment->getContext('EzSystems\RepositoryForms\Features\Context\ContentType');
    }

    /**
     * @Given a Content Type with a(n) :fieldTypeIdentifier field definition
     */
    public function aContentTypeWithAGivenFieldDefinition($fieldTypeIdentifier)
    {
        if (isset(self::$fieldTypeIdentifierMap[$fieldTypeIdentifier])) {
            $fieldTypeIdentifier = self::$fieldTypeIdentifierMap[$fieldTypeIdentifier];
        }

        $contentTypeCreateStruct = $this->contentTypeContext->newContentTypeCreateStruct();
        $contentTypeCreateStruct->addFieldDefinition(
            new FieldDefinitionCreateStruct(
                [
                    'identifier' => self::$fieldIdentifier,
                    'fieldTypeIdentifier' => $fieldTypeIdentifier,
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
        $this->visitPath(
            sprintf(
                '/content/create/nodraft/%s/eng-GB/2',
                $this->contentTypeContext->getCurrentContentType()->identifier
            )
        );
    }

    /**
     * @Then /^the edit form should contain a fieldset named after the field definition$/
     */
    public function theEditFormShouldContainAFieldsetNamedAfterTheFieldDefinition()
    {
        $this->assertSession()->elementTextContains(
            'css',
            sprintf('div.ezfield-identifier-%s fieldset legend', self::$fieldIdentifier),
            'Field'
        );
    }

    /**
     * @Given it should contain a :type input field
     */
    public function itShouldContainAGivenTypeInputField($inputType)
    {
        $this->assertSession()->elementExists(
            'css',
            sprintf(
                'div.ezfield-identifier-%s fieldset input[type=%s]',
                self::$fieldIdentifier,
                $inputType
            )
        );
    }

    /**
     * @Given /^it should contain the following set of labels, and input fields of the following types:$/
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

                    $this->assertSession()->elementExists('css', "label[for=$inputId]");
                    $this->assertSession()->elementTextContains(
                        'css',
                        "label[for=$inputId]",
                        $fieldExpectation['label']
                    );

                    unset($fieldsExpectations[$expectationId]);
                    reset($fieldsExpectations);
                    break;
                }
            }
        }

        Assertion::assertEmpty(
            $fieldsExpectations,
            'The following input fields were not found:' .
            implode(', ', array_map(function ($v) {return $v['label'];}, $fieldsExpectations))
        );
    }

    /**
     * @Given /^the field definition is required$/
     * @Given /^the field definition is marked as required$/
     */
    public function theFieldDefinitionIsMarkedAsRequired()
    {
        $this->contentTypeContext->updateFieldDefinition(
            self::$fieldIdentifier,
            new FieldDefinitionUpdateStruct(['isRequired' => true])
        );
    }

    /**
     * @Then /^the value input fields should be flagged as required$/
     */
    public function theInputFieldsShouldBeFlaggedAsRequired()
    {
        $inputNodeElements = $this->getSession()->getPage()->findAll(
            'css',
            sprintf('div.ezfield-identifier-%s fieldset input', self::$fieldIdentifier)
        );
        Assertion::assertNotEmpty($inputNodeElements, 'The input field is not marked as required');
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

    /**
     * Set a field definition option $option to $value.
     *
     * @param $option string The field definition option
     * @param $value mixed The option value
     */
    public function setFieldDefinitionOption($option, $value)
    {
        $this->contentTypeContext->updateFieldDefinition(
            self::$fieldIdentifier,
            new FieldDefinitionUpdateStruct(['fieldSettings' => [$option => $value]])
        );
    }
}
