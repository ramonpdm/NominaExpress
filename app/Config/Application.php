<?php

namespace App\Config;

use Throwable;

use App\Controllers\Frontend\BaseController;
use App\Controllers\Backend\APIController;

class Application
{
    private static ORM $orm;
    private Routes $routes;

    public function __construct()
    {
        // Bootstrap the static files
        $this->bootstrap();

        !session_id() && session_start();
        date_default_timezone_set(APP_TIMEZONE);

        self::$orm = new ORM();
        $this->routes = new Routes($this->getOrm());
    }

    public static function getOrm(): ORM
    {
        return self::$orm;
    }

    public function getRoutes(): Routes
    {
        return $this->routes;
    }

    /**
     * Load the required files that composer does not autoload.
     */
    private function bootstrap(): void
    {
        require_once 'Constants.php';
    }

    /**
     * Initialize the application by bootstrapping the application
     * and running the routes.
     */
    public function initialize(): string
    {
        // Start output buffering to store all the
        // echoed content in the buffer.
        ob_start();

        // Get the default controller instance
        // to be able to output error messages.
        $controller = Routes::isEndpoint() ? new ApiController($this->getOrm()) : new BaseController($this->getOrm());

        // Try to run the application, catch known exceptions
        // and throw unknown exceptions.
        try {
            // Check if the database connection is valid
            $this->getOrm()->valid();

            // Run the application
            $this->getRoutes()->run();
        } catch (Throwable $e) {
            // If the exception is unknown, send a default error message
            return $this->sendError($controller, $e);
        } finally {
            unset($controller);
        }

        return '';
    }

    /**
     * Send an error message to the client.
     */
    private function sendError(APIController|BaseController $controller, Throwable $e): string
    {
        try {
            if ($controller instanceof ApiController) {
                return $controller->sendOutput(exception: $e);
            } else {
                return $controller->renderView('errors/exceptions', ['title' => 'Error'], exception: $e);
            }
        } catch (Throwable $e) {
            die('An error occurred while sending the error message: ' . $e->getMessage());
        }
    }

    public static function isDevelopment(): bool
    {
        return $_ENV['ENVIRONMENT'] === 'development';
    }

    public static function isTesting(): bool
    {
        return $_ENV['ENVIRONMENT'] === 'testing';
    }
}
