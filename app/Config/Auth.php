<?php

namespace App\Config;

use App\Entities\Usuario;

class Auth
{
    public static function user(): ?Usuario
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if ($userId === null) {
            return null;
        }

        $user = Application::getOrm()->getRepo(Usuario::class)->find($userId);

        if ($user === null) {
            unset($_SESSION['user']);
            return null;
        }

        return $user;
    }

    public static function isLogged(): bool
    {
        return self::user() !== null;
    }

    public static function checkLogin(): void
    {
        if (!self::isLogged()) {
            header('Location: /login');
            exit();
        }
    }

    public static function requireAdmin(): void
    {
        self::checkLogin();
        if (!self::user()->isAdmin()) {
            header('Location: /');
            exit();
        }
    }

    public static function requireEditor(): void
    {
        self::checkLogin();
        if (!self::user()->puedeEditar()) {
            header('Location: /');
            exit();
        }
    }
}
