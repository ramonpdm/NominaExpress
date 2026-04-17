<?php

namespace App\Seeders;

use App\Entities\Usuario;
use App\Enums\RolUsuario;

class UsuarioSeeder extends BaseSeeder
{
    public function data(): array
    {
        return [
            new Usuario([
                'username' => 'admin',
                'password' => password_hash('contraseña', PASSWORD_BCRYPT),
                'nombre' => 'Ramón',
                'apellido' => 'Perdomo',
                'email' => 'admin@techsoft-rd.do',
                'rol' => RolUsuario::ADMIN,
            ]),
            new Usuario([
                'username' => 'rrhh',
                'password' => password_hash('contraseña', PASSWORD_BCRYPT),
                'nombre' => 'Hensy',
                'apellido' => 'Domínguez',
                'email' => 'rrhh@techsoft-rd.do',
                'rol' => RolUsuario::RRHH,
            ]),
            new Usuario([
                'username' => 'consulta',
                'password' => password_hash('contraseña', PASSWORD_BCRYPT),
                'nombre' => 'Eudys',
                'apellido' => 'Ramírez',
                'email' => 'consulta@techsoft-rd.do',
                'rol' => RolUsuario::CONSULTA,
            ]),
        ];
    }
}
