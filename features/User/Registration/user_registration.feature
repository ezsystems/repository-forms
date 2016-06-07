Feature: User registration form
    In order to allow users to create an account on a site
    As a site owner
    I want to expose a user registration form

Scenario: Registration is disabled for users who do not have the "user/register" policy
    Given I do not have the user/register policy
     When I go to "/register"
     Then I see an error message saying that I can not register

Scenario: A new user account can be registered from "/register"
    Given that I am not logged in
      And that the Anonymous user has the "user/register" policy
     When I go to "/register"
     Then a form is displayed
      And it matches the structure of the configured registration user Content Type
      And it has a register button
     When I fill in the form with valid values
      And I click on the register button
     Then I get redirected to "/register-confirmation"
      And I see a registration confirmation message
