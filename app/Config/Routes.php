<?php

namespace App\Config;

use Closure;
use Steampixel\Route;

use App\Controllers\Backend\APIController;
use App\Controllers\Frontend\AuthController;
use App\Controllers\Frontend\BaseController;
use App\Controllers\Frontend\DashboardController;
use App\Controllers\Frontend\EmpleadosController;
use App\Controllers\Frontend\DepartamentosController;
use App\Controllers\Frontend\CargosController;
use App\Controllers\Frontend\ConceptosController;
use App\Controllers\Frontend\PeriodosController;
use App\Controllers\Frontend\NominaController;
use App\Controllers\Frontend\ReportesController;

class Routes
{
    private ?Controller $engine = null;
    private ORM $orm;

    public function __construct(ORM $orm)
    {
        $this->orm = $orm;
    }

    public function run(string $url = '/'): void
    {
        $this->engine = $this->getEngine();
        $this->set();
        Route::run($url);
    }

    public function set(): void
    {
        Route::pathNotFound(function () {
            if ($this->isEndpoint()) {
                return $this->getEngine()->sendOutput(404);
            }
            return $this->getEngine()->renderView(404);
        });

        /* ---- API AUTO ROUTES ---- */
        Route::add('/api/v1/(.*?)(?:/(.*?))?(?:/(.*?))?', $this->call(), ['get', 'post', 'put', 'patch', 'delete']);

        /* ---- AUTH ---- */
        Route::add('/', $this->call([DashboardController::class, 'index']));
        Route::add('/login', $this->call([AuthController::class, 'login']));
        Route::add('/login', $this->call([AuthController::class, 'login']), method: 'post');
        Route::add('/logout', $this->call([AuthController::class, 'logout']));

        /* ---- EMPLEADOS ---- */
        Route::add('/empleados', $this->call([EmpleadosController::class, 'index']));
        Route::add('/empleados/nuevo', $this->call([EmpleadosController::class, 'nuevo']));
        Route::add('/empleados/nuevo', $this->call([EmpleadosController::class, 'guardar']), method: 'post');
        Route::add('/empleados/([0-9]+)', $this->call([EmpleadosController::class, 'ver']));
        Route::add('/empleados/([0-9]+)/editar', $this->call([EmpleadosController::class, 'editar']));
        Route::add('/empleados/([0-9]+)/editar', $this->call([EmpleadosController::class, 'actualizar']), method: 'post');

        /* ---- ADMINISTRACIÓN ---- */
        Route::add('/departamentos', $this->call([DepartamentosController::class, 'index']));
        Route::add('/departamentos', $this->call([DepartamentosController::class, 'guardar']), method: 'post');
        Route::add('/cargos', $this->call([CargosController::class, 'index']));
        Route::add('/cargos', $this->call([CargosController::class, 'guardar']), method: 'post');
        Route::add('/conceptos', $this->call([ConceptosController::class, 'index']));
        Route::add('/conceptos', $this->call([ConceptosController::class, 'guardar']), method: 'post');

        /* ---- NÓMINA ---- */
        Route::add('/periodos', $this->call([PeriodosController::class, 'index']));
        Route::add('/periodos', $this->call([PeriodosController::class, 'guardar']), method: 'post');
        Route::add('/periodos/([0-9]+)/cerrar', $this->call([PeriodosController::class, 'cerrar']), method: 'post');

        Route::add('/nomina/procesar/([0-9]+)', $this->call([NominaController::class, 'procesar']));
        Route::add('/nomina/procesar/([0-9]+)', $this->call([NominaController::class, 'ejecutar']), method: 'post');
        Route::add('/nomina/comprobante/([0-9]+)', $this->call([NominaController::class, 'comprobante']));

        /* ---- REPORTES ---- */
        Route::add('/reportes/nomina', $this->call([ReportesController::class, 'nomina']));
        Route::add('/reportes/empleados', $this->call([ReportesController::class, 'empleados']));
        Route::add('/reportes/departamentos', $this->call([ReportesController::class, 'departamentos']));

        /* ---- FALLBACK ---- */
        Route::add('/(.*?)(?:/(.*?))?(?:/(.*?))?(?:/(.*?))?', $this->call());
    }

    public function call(array $controllerAndMethod = []): Closure
    {
        return function (...$pathArgs) use ($controllerAndMethod) {
            // If a callable is provided, just call it
            if (!empty($controllerAndMethod)) {
                [&$controllerClass] = $controllerAndMethod;
                $controllerClass = new $controllerClass($this->orm);
                return call_user_func_array($controllerAndMethod, $pathArgs);
            }

            // Otherwise, use the engine to identify the
            // controller and method from the URL
            return $this->getEngine()->init(...$pathArgs);
        };
    }

    public function getEngine(): Controller|BaseController|APIController
    {
        if ($this->engine !== null) {
            return $this->engine;
        }

        if ($this->isEndpoint()) {
            return new APIController($this->orm);
        }

        return new BaseController($this->orm);
    }

    public static function isEndpoint(): bool
    {
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            return true;
        }

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            return true;
        }

        return preg_match('/(api)\/v[0-9]+/', $_SERVER['REQUEST_URI']);
    }

    public static function isPOST(): bool   { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
    public static function isGET(): bool    { return $_SERVER['REQUEST_METHOD'] === 'GET'; }
    public static function isPUT(): bool    { return $_SERVER['REQUEST_METHOD'] === 'PUT'; }
    public static function isPATCH(): bool  { return $_SERVER['REQUEST_METHOD'] === 'PATCH'; }
    public static function isDELETE(): bool { return $_SERVER['REQUEST_METHOD'] === 'DELETE'; }
    public static function isOPTIONS(): bool{ return $_SERVER['REQUEST_METHOD'] === 'OPTIONS'; }
}
