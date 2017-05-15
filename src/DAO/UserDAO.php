<?php

namespace App\DAO;

class UserDAO
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getByUsernameAndPassword($username, $password)
    {
        $user = $this->conn->fetchAssoc("SELECT * FROM user WHERE username = ?", array($username));

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }
}