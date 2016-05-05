Feature: User field value edit form
    In order to edit content of ezuser fields
    As an integrator
    I want the ezuser field form to implement the FieldType's behaviour

Background:
    Given a Content Type with a user field definition

Scenario: The attributes of a user field have a form representation
    When I view the edit form for this field
    Then the edit form should contain a fieldset named after the user field definition
     And it should contain the following set of labels and input fields types:
         | label | type |
         | Username | text |
         | Password | password |
         | Confirm password | password |
         | E-mail | email |

Scenario: The input fields are flagged as required when the field definition is required
    Given the user field is required
     When I view the edit form for this field
     Then the user input fields should be flagged as required
