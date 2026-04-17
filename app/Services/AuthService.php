<?php

namespace App\Services;

use DateTime;
use App\Config\Application;
use App\Entities\Usuario;
use App\Exceptions\Exception;

class AuthService
{
    public function __construct()
    {
        throw new \Exception('No se puede instanciar AuthService directamente. Usar solo de manera estática.');
    }

    public static function login(string $username, string $password): void
    {
        $repo = Application::getOrm()->getRepo(Usuario::class);
        $user = $repo->findOneBy(['username' => $username]);

        if (
            !$user instanceof Usuario
            || !$user->activo
            || !password_verify($password, $user->password)
        ) {
            throw new Exception('Usuario y/o contraseña incorrectos');
        }

        $user->ultimo_acceso = new DateTime();
        Application::getOrm()->getProvider()->flush();

        $_SESSION['user'] = [
            'id' => $user->id,
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'username' => $user->username,
            'rol' => $user->rol->value,
        ];
    }
}
