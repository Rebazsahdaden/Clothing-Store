<?php
class Admin {
    public function simpleLogin($username, $password) {
        // Replace with your actual database check
        return $username === 'admin' && $password === 'admin123';
    }
}
