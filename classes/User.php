<?php
// classes/User.php

class User {
    public $id;
    public $username;
    public $email;
    public $role;

    public function __construct($id, $username, $email, $role = 'staff') {
        $this->id       = $id;
        $this->username = $username;
        $this->email    = $email;
        $this->role     = $role;
    }

    // Register a new user
    public static function register($username, $email, $password) {
        global $pdo;

        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        return $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashedPassword]);
    }


}

?>
