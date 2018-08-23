<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Behat\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;
use PHPUnit\Framework\Assert as Assertion;

final class SelectionFieldTypeFormContext extends RawMinkContext implements SnippetAcceptingContext
{
    private static $fieldIdentifier = 'field';

    /**
     * @var \EzSystems\RepositoryForms\Behat\Context\FieldTypeFormContext
     */
    private $fieldTypeFormContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $this->fieldTypeFormContext = $scope->getEnvironment()->getContext(
            'EzSystems\RepositoryForms\Behat\Context\FieldTypeFormContext'
        );
    }

    /**
     * @Given /^the field definition is set to single choice$/
     */
    public function setFieldDefinitionToSingleChoice()
    {
        $this->fieldTypeFormContext->setFieldDefinitionOption('isMultiple', false);
    }

    /**
     * @Given /^the field definition is set to multiple choice$/
     */
    public function setFieldDefinitionToMultipleChoice()
    {
        $this->fieldTypeFormContext->setFieldDefinitionOption('isMultiple', true);
    }

    /**
     * @Then it should contain a select field
     */
    public function itShouldContainASelectField()
    {
        $this->assertSession()->elementExists(
            'css',
            sprintf(
                'form[name="ezrepoforms_content_edit"] '
                . 'select[name="ezrepoforms_content_edit[fieldsData][%s][value]"]',
                self::$fieldIdentifier
            )
        );
    }

    /**
     * @Then the select field should be flagged as required
     */
    public function theSelectFieldShouldBeFlaggedAsRequired()
    {
        $nodeElements = $this->getSession()->getPage()->findAll(
            'css',
            sprintf(
                'select[name="ezrepoforms_content_edit[fieldsData][%s][value][]"]',
                self::$fieldIdentifier
            )
        );
        Assertion::assertNotEmpty($nodeElements, 'The select field is not marked as required');
        foreach ($nodeElements as $nodeElement) {
            Assertion::assertEquals(
                'required',
                $nodeElement->getAttribute('required'),
                sprintf(
                    'select with id %s is not flagged as required',
                    $nodeElement->getAttribute('id')
                )
            );
        }
    }

    /**
     * @Then the input is a single selection dropdown
     */
    public function theInputIsASingleSelectionDropdown()
    {
        $selector = sprintf(
            'select[name="ezrepoforms_content_edit[fieldsData][%s][value]"]',
            self::$fieldIdentifier
        );

        $this->assertSession()->elementExists('css', $selector);
        $this->assertSession()->elementNotContains('css', $selector, 'multiple="multiple"');
    }

    /**
     * @Then the input is a multiple selection dropdown
     */
    public function theInputIsAMultipleSelectionDropdown()
    {
        $selector = sprintf(
            'select[name="ezrepoforms_content_edit[fieldsData][%s][value][]"][multiple=multiple]',
            self::$fieldIdentifier
        );

        $this->assertSession()->elementExists('css', $selector);
    }
}
