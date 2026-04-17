# Requisitos

- PHP 8.4
- MySQL 8.0
- Composer
- Docker

# Instalación

1. Instalar Docker.
2. Ejecutar el siguiente comando para crear un contenedor de Docker:

```bash
docker compose up -d
``` 

3. Instalar las dependencias del proyecto:

```bash
composer install
```

4. Crear un archivo de configuración de entorno `.env` en la raíz del proyecto y agregar las siguientes variables de
   entorno:

```
# Database Connection Details
DB_DRIVER=pdo_mysql
DB_USER=root
DB_PASS=REPLACE_WITH_DB_PASSWORD
DB_NAME=rentroad
DB_HOST=REPLACE_WITH_DB_HOST
DB_PORT=3306

# Application Environment Variables
ENVIRONMENT=development
```

> **Nota:** Asegúrate de reemplazar `REPLACE_WITH_DB_PASSWORD` y `REPLACE_WITH_DB_HOST` con la contraseña y el host de
> tu base de datos MySQL (ejecuta `docker ps` para ver el contenedor de la base de datos y su host).

5. Ejecuta el siguiente comando para crear la base de datos:

```bash
./cli db:update
```

6. Acceder a RentRoad en tu navegador:

```
http://127.0.0.1:3002
```

# Rutas, Controladores, Entidades (Modelos) y Vistas

A continuación se explica, con ejemplos y rutas a archivos del proyecto, qué son las rutas, los controladores, las entidades (modelos) y las vistas, y dónde configurarlos o extenderlos.

1) Rutas

- Qué son: Las rutas mapean URLs a controladores y métodos. Determinan qué código se ejecuta cuando llega una petición HTTP.
- Dónde se configuran: `app\Config\Routes.php`.
- Ejemplo breve: en `app\Config\Routes.php` hay reglas como:
  - `Route::add('/api/v1/(.*?)(?:/(.*?))?(?:/(.*?))?', ...)` — ruta automática para la API (mapea `/api/v1/vehicles`, `/api/v1/vehicles/1`, etc.).
  - `Route::add('/', $this->call(HomeController::class));` — ruta para la página principal.

- Cómo añadir una ruta manual: edita `app\Config\Routes.php` y añade una línea similar a:
  - `Route::add('/mis-recursos', $this->call(\App\Controllers\Frontend\MiController::class, 'index'));

2) Controladores

- Qué son: Los controladores contienen la lógica que responde a una petición. Reciben parámetros (de la URL, formulario o JSON), usan repositorios/entidades para obtener o modificar datos y devuelven una vista o una respuesta JSON.
- Dónde están:
  - Frontend: `app\Controllers\Frontend\` (por ejemplo `app\Controllers\Frontend\VehiclesController.php`).
  - Backend / API: `app\Controllers\Backend\` (por ejemplo `app\Controllers\Backend\VehiclesController.php`).

- Ejemplo (archivo real): `app\Controllers\Backend\VehiclesController.php` contiene métodos como:
  - `findAll()` — obtiene todos los vehículos y devuelve JSON para la API (`/api/v1/vehicles`).
  - `insert()` — crea un nuevo vehículo leyendo `$_POST` y persistiendo la entidad.
  - `delete($id)` — elimina un vehículo por id.

- Añadir un controlador: crea un archivo nuevo en la carpeta adecuada, usa el namespace correspondiente y extiende `APIController` (para endpoints) o `BaseController` (para frontend). Luego registra la ruta en `app\Config\Routes.php`.

3) Entidades (Modelos)

- Qué son: Las entidades representan las tablas y la estructura de la base de datos (modelo del dominio). Aquí se usan las anotaciones/atributos de Doctrine para mapear propiedades a columnas y relaciones.
- Dónde están: `app\Entities\` (por ejemplo `app\Entities\Vehiculo.php`).

- Ejemplo (archivo real): `app\Entities\Vehiculo.php`
  - Define propiedades como `marca`, `modelo`, `ano`, `placa`, `categoria` (relación ManyToOne), `sucursal`, `costo`, etc.
  - Incluye métodos del dominio, por ejemplo `getCosto()`, `getCostoSeguro()` y `getCostoTotal()` para calcular precios.

- Modificar o crear entidades: crea/edita archivos en `app\Entities\` y usa atributos de Doctrine (p. ej. `#[ORM\Column]`, `#[ORM\ManyToOne]`). Después de cambiar el modelo, actualiza la base de datos (según el flujo del proyecto: hay comandos en `cli` para migrar/actualizar la BD).

4) Vistas

- Qué son: Las vistas son plantillas HTML/PHP que muestran la interfaz al usuario. Se renderizan desde un controlador frontend pasando datos (por ejemplo: listas, formularios, variables `title`).
- Dónde están: `app\Views\` y subcarpetas por secciones (`app\Views\vehicles\index.php`, `app\Views\inc\header.php`, `app\Views\inc\footer.php`, etc.).

- Ejemplo (archivo real): `app\Views\vehicles\index.php` — muestra la lista de vehículos, incluye el header/ footer y contiene JavaScript para cargar dinámicamente los vehículos desde la API (`/api/v1/vehicles`).
  - El controlador frontend `app\Controllers\Frontend\VehiclesController.php` llama a `return $this->renderView('vehicles/index', [...])` para mostrar esta vista con datos (categorías y sucursales).

5) Flujo típico (resumen)

- Petición a una URL (p. ej. `GET /vehicles`)
  1. `app\Config\Routes.php` determina qué controlador/método manejará la petición.
  2. El controlador (`app\Controllers\Frontend\VehiclesController.php`) recopila datos (consultando repositorios/entidades) y llama a `renderView(...)` o devuelve JSON.
  3. Si es una vista, la plantilla en `app\Views\...` se renderiza y se muestra al usuario.

6) Ejemplos prácticos (rápidos)

- Ver todos los vehículos en la API: `GET /api/v1/vehicles` — implementado por `app\Controllers\Backend\VehiclesController.php::findAll()`.
- Página de vehículos: `GET /vehicles` — implementado por `app\Controllers\Frontend\VehiclesController.php::index()` y la vista `app\Views\vehicles\index.php`.
- Crear un vehículo via API: `POST /api/v1/vehicles` — implementado por `app\Controllers\Backend\VehiclesController.php::insert()` (lee `$_POST`).

7) ¿Dónde debo editar para extender funcionalidad?

- Añadir una nueva URL -> `app\Config\Routes.php`.
- Añadir la lógica para esa URL -> crear/editar un controlador en `app\Controllers\Frontend` o `app\Controllers\Backend`.
- Definir los datos -> crear/editar entidades en `app\Entities` y repositorios en `app\Repositories`.
- Mostrar/editar la interfaz -> crear/editar plantillas en `app\Views`.