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
use Behat\MinkExtension\Context\RawMinkContext;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Repository\Values\User\RoleCreateStruct;
use EzSystems\PlatformBehatBundle\Context\RepositoryContext;

class UserRegistrationContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    use RepositoryContext;

    private static $password = 'publish';
    private static $language = 'eng-GB';
    private static $groupId = 4;

    /**
     * @injectService $repository @ezpublish.api.repository
     */
    public function __construct(Repository $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * @Given /^I do not have the user\/register policy$/
     */
    public function iDoNotHaveTheUserRegisterPolicy()
    {
        $role = $this->createRegistrationRole(false);
        $user = $this->createUserWithRole($role);
        $this->loginAs($user);
    }

    /**
     * @Given /^I do have the user\/register policy$/
     */
    public function iDoHaveTheUserRegisterPolicy()
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
    public function loginAs(User $user)
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
}
