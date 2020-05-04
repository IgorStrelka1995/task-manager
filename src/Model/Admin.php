<?php

namespace App\Model;

use libs\Db\Db;
use PDO;

class Admin
{
    /**
     * Get admin
     *
     * @access public
     * @param string $login
     * @return array|boolean
     */
    public function getAdminUser(string $login)
    {
        $query = "SELECT id, login, password FROM admin WHERE login = :login";

        $stmt = Db::prepare($query, [
            ':login' => $login
        ])->fetch(PDO::FETCH_ASSOC);

        return $stmt;    
    }
}