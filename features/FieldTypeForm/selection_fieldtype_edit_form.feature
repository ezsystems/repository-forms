Feature: Selection field value edit form
    In order to edit content of a Selection field
    As an integrator
    I want the Selection field form to implement the FieldType's behaviour

Background:
    Given a Content Type with an ezselection field definition

Scenario: The attributes of the field have a form representation
    Given I view the edit form for this field
     Then the edit form should contain an identifiable widget for ezselection field definition
      And it should contain a select field

Scenario: The options added to a field definition have a form representation
    Given I add the following options to the field definition:
          | one |
          | two |
          | three |
     When I view the edit form for this field
     Then the form element should contain the following values:
          | one |
          | two |
          | three |

Scenario: The select field is flagged as required when the field definition is required and set to multiple choice
    Given the field definition is required
      And the field definition is set to multiple choice
     When I view the edit form for this field
     Then the select field should be flagged as required

Scenario: The multiple/single checkbox is honored
    Given the field definition is set to single choice
     When I view the edit form for this field
     Then the input is a single selection dropdown
     When the field definition is set to multiple choice
      And I view the edit form for this field
     Then the input is a multiple selection dropdown

Scenario: The first item of the required select field is selected by default when editing
    Given I add some items to the field definition
      And the field definition is required
      And the field definition is set to single choice
     When I view the edit form for this field
     Then the first item in the list is selected
    Given the field definition is set to multiple choice
     When I view the edit form for this field
     Then the first item in the list is selected

Scenario: The selected item(s) are correctly saved
    Given I add the following options to the field definition:
          | uno |
          | dos |
          | tres |
      And the field definition is set to single choice
      And I view the edit form for this field
      And I select the item "dos"
     When I submit the form
      And I view the Content that was created
     Then the field value is "dos"
    Given the field definition is set to multiple choice
      And I view the edit form for this field
      And I select these items:
          | uno |
          | tres |
     When I submit the form
      And I view the Content that was created
     Then the field value is:
          | uno |
          | tres |
