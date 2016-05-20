<?php
/**
 * Created by PhpStorm.
 * User: bdunogier
 * Date: 19/05/2016
 * Time: 18:39
 */

namespace EzSystems\RepositoryForms\Data\User;


class UserAccountFieldData
{
    public $username;

    public $password;

    public $email;

    public function __construct($username, $password, $email)
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
    }
}
