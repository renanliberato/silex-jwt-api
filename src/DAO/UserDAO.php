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
        $id = $this->conn->fetchColumn("SELECT id FROM user WHERE username = ? AND password = ?", array($username, $password));

        return $id;
    }
}