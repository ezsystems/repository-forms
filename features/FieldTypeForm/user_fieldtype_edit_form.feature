Feature: User field value edit form
    In order to edit content of ezuser fields
    As an integrator
    I want the ezuser field form to implement the FieldType's behaviour

Background:
    Given a Content Type with a user field definition

Scenario: The attributes of a user field have a form representation
    When I view the edit user form for this field
    Then the edit form should contain an identifiable widget for user field definition
     And it should contain the following set of labels, and input fields of the following types:
         | label | type |
         | Username | text |
         | Password | password |
         | Confirm password | password |
         | Email | email |

Scenario: The input fields are flagged as required when the field definition is required
    Given the field definition is marked as required
     When I view the edit user form for this field
     Then the value input fields for user field should be flagged as required
