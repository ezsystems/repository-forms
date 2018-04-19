Feature: TextLine field value edit form
    In order to edit content of a TextLine fields
    As an integrator
    I want the ezuser field form to implement the FieldType's behaviour

Background:
    Given a Content Type with a textline field definition

Scenario: The attributes of a textline field have a form representation
    When I view the edit form for this field
    Then the edit form should contain an identifiable widget for textline field definition
     And it should contain a text input field

Scenario: The input fields are flagged as required when the field definition is required
    Given the field definition is required
     When I view the edit form for this field
     Then the value input fields for textline field should be flagged as required
