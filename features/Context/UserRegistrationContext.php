<?php
/**
 * This file is part of the ezplatform package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\RawMinkContext;
use eZ\Bundle\EzPublishCoreBundle\Features\Context\YamlConfigurationContext;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\API\Repository\Values\User\UserGroup;
use eZ\Publish\Core\Repository\Values\User\RoleCreateStruct;
use EzSystems\PlatformBehatBundle\Context\RepositoryContext;
use PHPUnit\Framework\Assert as Assertion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class UserRegistrationContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    use RepositoryContext;

    private static $password = 'publish';

    private static $language = 'eng-GB';

    private static $groupId = 4;

    private $registrationUsername;

    /**
     * Used to cover registration group customization.
     * @var UserGroup
     */
    private $customUserGroup;

    /**
     * @var YamlConfigurationContext
     */
    private $yamlConfigurationContext;

    /**
     * @injectService $repository @ezpublish.api.repository
     */
    public function __construct(Repository $repository)
    {
        $this->setRepository($repository);
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $this->yamlConfigurationContext = $scope->getEnvironment()->getContext(
            'eZ\Bundle\EzPublishCoreBundle\Features\Context\YamlConfigurationContext'
        );
    }

    /**
     * @Given /^I do not have the user\/register policy$/
     */
    public function loginAsUserWithoutRegisterPolicy()
    {
        $role = $this->createRegistrationRole(false);
        $user = $this->createUserWithRole($role);
        $this->loginAs($user);
    }

    /**
     * @Given /^I do have the user\/register policy$/
     */
    public function loginAsUserWithUserRegisterPolicy()
    {
        $role = $this->createRegistrationRole(true);
        $user = $this->createUserWithRole($role);
        $this->loginAs($user);
    }

    /**
     * Creates a user for registration testing, and assigns it the role $role.
     *
     * @param Role $role
     *
     * @return User
     */
    private function createUserWithRole(Role $role)
    {
        $userService = $this->getRepository()->getUserService();
        $username = uniqid($role->identifier);
        $createStruct = $userService->newUserCreateStruct(
            $username,
            $username . '@example.com',
            self::$password,
            'eng-GB'
        );
        $createStruct->setField('first_name', $username);
        $createStruct->setField('last_name', 'The first');
        $user = $userService->createUser($createStruct, [$userService->loadUserGroup(self::$groupId)]);

        $this->getRepository()->getRoleService()->assignRoleToUser($role, $user);

        return $user;
    }

    /**
     * Creates a role for user registration test.
     *
     * It always has the minimal set of policies to operate (user/login and content/read).
     *
     * @param bool $withUserRegisterPolicy Determines if the role gets the user/register policy
     *
     * @return Role
     */
    private function createRegistrationRole($withUserRegisterPolicy = true)
    {
        $roleIdentifier = uniqid(
            'anonymous_role_' . ($withUserRegisterPolicy ? 'with' : 'without') . '_register'
        );

        $roleService = $this->getRepository()->getRoleService();
        $roleCreateStruct = new RoleCreateStruct(['identifier' => $roleIdentifier]);
        $roleCreateStruct->addPolicy($roleService->newPolicyCreateStruct('user', 'login'));
        $roleCreateStruct->addPolicy($roleService->newPolicyCreateStruct('content', 'read'));

        if ($withUserRegisterPolicy === true) {
            $roleCreateStruct->addPolicy($roleService->newPolicyCreateStruct('user', 'register'));
        }

        $roleService->publishRoleDraft($roleService->createRole($roleCreateStruct));

        return $roleService->loadRoleByIdentifier($roleIdentifier);
    }

    /**
     * @Then /^I see an error message saying that I can not register$/
     */
    public function iSeeAnErrorMessageSayingThatICanNotRegister()
    {
        $this->assertSession()->pageTextContains('You are not allowed to register a new account');
    }

    /**
     * @param User $user
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    private function loginAs(User $user)
    {
        $this->visitPath('/login');
        $page = $this->getSession()->getPage();
        $page->fillField('username', $user->login);
        $page->fillField('password', self::$password);
        $page->findButton('Login')->press();
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Then /^I can see the registration form$/
     */
    public function iCanSeeTheRegistrationForm()
    {
        $this->assertSession()->pageTextNotContains('You are not allowed to register a new account');
        $this->assertSession()->elementExists('css', 'form[name=ezrepoforms_user_register]');
    }

    /**
     * @Given /^it matches the structure of the configured registration user Content Type$/
     */
    public function itMatchesTheStructureOfTheConfiguredRegistrationUserContentType()
    {
        $userContentType = $this->getRepository()->getContentTypeService()
            ->loadContentTypeByIdentifier('user');
        foreach ($userContentType->getFieldDefinitions() as $fieldDefinition) {
            $this->assertSession()->elementExists(
                'css',
                sprintf(
                    'div.ezfield-type-%s.ezfield-identifier-%s',
                    $fieldDefinition->fieldTypeIdentifier,
                    $fieldDefinition->identifier
                )
            );
        }
    }

    /**
     * @Given /^it has a register button$/
     */
    public function itHasARegisterButton()
    {
        $this->assertSession()->elementExists(
            'css',
            'form[name=ezrepoforms_user_register] button[type=submit]'
        );
    }

    /**
     * @When /^I fill in the form with valid values$/
     */
    public function iFillInTheFormWithValidValues()
    {
        $page = $this->getSession()->getPage();

        $this->registrationUsername = uniqid('registration_username_');

        $page->fillField('ezrepoforms_user_register[fieldsData][first_name][value]', 'firstname');
        $page->fillField('ezrepoforms_user_register[fieldsData][last_name][value]', 'firstname');
        $page->fillField('ezrepoforms_user_register[fieldsData][user_account][value][username]', $this->registrationUsername);
        $page->fillField('ezrepoforms_user_register[fieldsData][user_account][value][email]', $this->registrationUsername . '@example.com');
        $page->fillField('ezrepoforms_user_register[fieldsData][user_account][value][password][first]', self::$password);
        $page->fillField('ezrepoforms_user_register[fieldsData][user_account][value][password][second]', self::$password);
    }

    /**
     * @When /^I click on the register button$/
     */
    public function iClickOnTheRegisterButton()
    {
        $this->getSession()->getPage()->pressButton('ezrepoforms_user_register[publish]');
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Then /^I am on the registration confirmation page$/
     */
    public function iAmOnTheRegistrationConfirmationPage()
    {
        $this->assertSession()->addressEquals('/register-confirm');
    }

    /**
     * @Given /^I see a registration confirmation message$/
     */
    public function iSeeARegistrationConfirmationMessage()
    {
        $this->assertSession()->pageTextContains('Your account has been created');
    }

    /**
     * @Given /^the user account has been created$/
     */
    public function theUserAccountHasBeenCreated()
    {
        $this->getRepository()->getUserService()->loadUserByLogin($this->registrationUsername);
    }

    /**
     * @Given /^a User Group$/
     */
    public function createUserGroup()
    {
        $userService = $this->getRepository()->getUserService();

        $groupCreateStruct = $userService->newUserGroupCreateStruct(self::$language);
        $groupCreateStruct->setField('name', uniqid('User registration group '));
        $this->customUserGroup = $userService->createUserGroup(
            $groupCreateStruct,
            $userService->loadUserGroup(self::$groupId)
        );
    }

    /**
     * @Given /^the following user registration group configuration:$/
     */
    public function addUserRegistrationConfiguration(PyStringNode $extraConfigurationString)
    {
        $extraConfigurationString = str_replace(
            '<userGroupContentId>',
            $this->customUserGroup->id,
            $extraConfigurationString
        );

        $this->yamlConfigurationContext->addConfiguration(Yaml::parse($extraConfigurationString));
    }

    /**
     * @When /^I register a user account$/
     */
    public function iRegisterAUserAccount()
    {
        $this->loginAsUserWithUserRegisterPolicy();
        $this->visitPath('/register');
        $this->assertSession()->statusCodeEquals(200);
        $this->iFillInTheFormWithValidValues();
        $this->iClickOnTheRegisterButton();
        $this->iAmOnTheRegistrationConfirmationPage();
        $this->iSeeARegistrationConfirmationMessage();
    }

    /**
     * @Then /^the user is created in this user group$/
     */
    public function theUserIsCreatedInThisUserGroup()
    {
        $userService = $this->getRepository()->getUserService();
        $user = $userService->loadUserByLogin($this->registrationUsername);
        $userGroups = $userService->loadUserGroupsOfUser($user);

        Assertion::assertEquals(
            $this->customUserGroup->id,
            $userGroups[0]->id
        );
    }

    /**
     * @Given /^the following user registration templates configuration:$/
     */
    public function addRegistrationTemplatesConfiguration(PyStringNode $string)
    {
        $this->yamlConfigurationContext->addConfiguration(Yaml::parse($string));
    }

    /**
     * @Given /^the following template in "([^"]*)":$/
     */
    public function createTemplateAt($path, PyStringNode $contents)
    {
        $fs = new Filesystem();
        $fs->mkdir(dirname($path));
        $fs->dumpFile($path, $contents);
    }

    /**
     * @Then /^the confirmation page is rendered using the "([^"]*)" template$/
     * @Then /^the form is rendered using the "([^"]*)" template$/
     *
     * @param string $template
     *        The template path to look for.
     *        If relative to app/Resources/views (example: user/register.html.twig),
     *        the path is checked with the :path:file.html.twig syntax as well.
     */
    public function thePageIsRenderedUsingTheTemplateConfiguredIn($template)
    {
        $html = $this->getSession()->getPage()->getOuterHtml();
        $found = (strpos($html, sprintf('<!-- STOP %s -->', $template)) !== false);

        if (!$found && strpos($template, ':') === false) {
            $alternativeTemplate = sprintf(
                ':%s:%s',
                dirname($template),
                basename($template)
            );
            $found = (strpos($html, sprintf('<!-- STOP %s -->', $alternativeTemplate)) !== false);
        }

        Assertion::assertTrue(
            $found,
            "Couldn't find $template " .
            (isset($alternativeTemplate) ? "nor $alternativeTemplate " : ' ') .
            "in HTML:\n\n$html"
        );
    }
}
