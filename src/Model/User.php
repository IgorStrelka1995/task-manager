<?php

namespace App\Model;

use libs\Db\Db;

/**
 * @package App\Model
 */
class User
{
    /**
     * Create new user
     *
     * @access public
     * @param string $username
     * @param string $email
     * @return int id created user
     */
    public function createUser(string $username, string $email): int
    {
        $query = "INSERT INTO user (`username`, `email`) VALUES (:username, :email)";

        Db::prepare($query, [
            ':username' => $username,
            ':email'    => $email
        ]);

        return Db::lastInsertId();
    }
}