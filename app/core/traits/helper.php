<?php 

namespace Core;

trait Helper
{
    public function sanitizeInput($data)
    {
        return htmlspecialchars(strip_tags($data));
    }

    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
