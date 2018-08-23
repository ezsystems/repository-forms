Feature: User registration form
    In order to allow users to create an account on a site
    As a site owner
    I want to expose a user registration form

Scenario: Registration is disabled for users who do not have the "user/register" policy
    Given I do not have the user/register policy
     When I go to "/register"
     Then I see an error message saying that I can not register

Scenario: A new user account can be registered from "/register"
    Given I do have the user/register policy
     When I go to "/register"
      And I fill in the form with valid values
      And I click on the register button
     Then I am on the registration confirmation page
      And I see a registration confirmation message
      And the user account has been created

Scenario: The user group where registered users are created can be customized
    Given a User Group
      And the following user registration group configuration:
      """
      ezpublish:
        system:
          default:
            user_registration:
              group_id: <userGroupContentId>
          site_group:
            user_registration:
              group_id: <userGroupContentId>
      """
     When I register a user account
     Then the user is created in this user group

Scenario: The user registration templates can be customized
    Given I do have the user/register policy
      And the following user registration templates configuration:
      """
      ezpublish:
        system:
          default:
            user_registration:
              templates:
                form: 'AppBundle:user:registration_form.html.twig'
                confirmation: 'AppBundle:user:registration_confirmation.html.twig'
      """
      And the following template in "src/AppBundle/Resources/views/user/registration_form.html.twig":
      """
      {% extends noLayout is defined and noLayout == true ? viewbaseLayout : pagelayout %}

      {% block content %}
          <section class="ez-content-edit">
            {{ form_start(form) }}
            {{- form_widget(form.fieldsData) -}}
            {{ form_end(form) }}
           </section>
      {% endblock %}
      """
      And the following template in "src/AppBundle/Resources/views/user/registration_confirmation.html.twig":
      """
      {% extends noLayout is defined and noLayout == true ? viewbaseLayout : pagelayout %}

      {% block content %}
          <h1>Your account has been created</h1>
          <p class="user-register-confirmation-message">
              Thank you for registering an account. You can now <a href="{{ path('login') }}">login</a>.
          </p>
      {% endblock %}
      """
     When I go to "/register"
     Then the form is rendered using the "user/registration_form.html.twig" template
     When I register a user account
     Then the confirmation page is rendered using the "user/registration_confirmation.html.twig" template
