<?php

namespace App\Controllers\Frontend;

use Throwable;
use App\Config\Auth;
use App\Services\AuthService;

class AuthController extends BaseController
{
    public function login(): string
    {
        if (Auth::isLogged())
            return $this->redirect('/');

        $response = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            try {
                AuthService::login($username, $password);
                return $this->redirect('/');
            } catch (Throwable $e) {
                $response = [
                    'error' => true,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $this->renderView('auth/login', data: $response);
    }

    public function logout(): string
    {
        session_destroy();
        return $this->redirect('/login');
    }
}
