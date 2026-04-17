# Arquitectura Técnica

## Stack Tecnológico

| Capa            | Tecnología                            | Versión |
|-----------------|---------------------------------------|---------|
| Backend         | PHP                                   | 8.2+    |
| ORM             | Doctrine ORM                          | 3.x     |
| DB Abstraction  | Doctrine DBAL                         | 4.x     |
| Base de datos   | MySQL                                 | 8.0     |
| Routing         | Steampixel Simple PHP Router          | 0.7.x   |
| Variables ENV   | vlucas/phpdotenv                      | 5.6     |
| Caché           | Symfony Cache                         | 7.2     |
| Frontend        | HTML5 + CSS3 + JavaScript ES6 vanilla | —       |
| Servidor web    | Apache 2.4 (vía contenedor PHP)       | —       |
| Contenerización | Docker + Docker Compose               | —       |
| Testing         | PHPUnit                               | 11.5    |

> **Decisión de diseño:** se usa **Doctrine ORM** en lugar de PDO directo. Esto nos da mapeo objeto-relacional, migraciones automáticas, repositorios reutilizables y QueryBuilder seguro contra inyección SQL. La capa de algoritmos académicos (QuickSort, Búsqueda Binaria, etc.) opera sobre las colecciones que devuelven los repositorios Doctrine.

## Patrón de Arquitectura

Patrón **MVC** adaptado a PHP con una capa adicional de **Servicios** y **Repositorios** (estilo Layered Architecture):

```
┌─────────────────────────────────────────────────────┐
│                    HTTP Request                      │
│                         ↓                            │
│              Steampixel Router (public/)             │
│                         ↓                            │
│              Controllers (app/Controllers)           │
│              ── valida entradas ──                   │
│              ── orquesta servicios ──                │
│                         ↓                            │
│              Services (app/Services)                 │
│              ── lógica de negocio ──                 │
│              ── algoritmos computacionales ──        │
│                         ↓                            │
│              Repositories (app/Repositories)         │
│                         ↓                            │
│              Doctrine ORM ↔ Entities (app/Entities)  │
│                         ↓                            │
│                       MySQL                          │
└─────────────────────────────────────────────────────┘
                          ↑
              Vistas PHP (app/Views) ← Controllers
```

### Responsabilidad por capa
- **Controllers:** reciben request, validan, llaman al servicio, eligen vista.
- **Services:** lógica de negocio (cálculo de nómina, generación de comprobantes, autenticación). **Aquí viven los algoritmos computacionales.**
- **Repositories:** consultas a la base de datos vía Doctrine.
- **Entities:** clases anotadas con atributos PHP 8 que mapean a tablas.
- **Views:** PHP plano con HTML; sin motor de plantillas.

## Estructura de Directorios (`src/`)

```
src/
├── app/
│   ├── Algorithms/        ← Implementaciones académicas reutilizables
│   │   ├── QuickSort.php
│   │   ├── BinarySearch.php
│   │   └── Estadistica.php
│   ├── Config/            ← Bootstrap de Doctrine, config global
│   ├── Controllers/       ← Un controlador por módulo (Empleado, Nomina, etc.)
│   ├── Entities/          ← Empleado, Departamento, Cargo, Nomina, etc.
│   ├── Enums/             ← EstadoEmpleado, TipoConcepto, RolUsuario
│   ├── Exceptions/        ← Excepciones de dominio
│   ├── Repositories/      ← Repositorios Doctrine
│   ├── Seeders/           ← Datos iniciales (52 empleados de prueba)
│   ├── Services/          ← NominaCalculator, AuthService, ReporteService
│   ├── Traits/            ← Traits compartidos (Timestamps, SoftDelete)
│   └── Views/             ← Plantillas PHP
├── cli/                   ← Scripts CLI (migraciones, seeders, doctrine commands)
├── public/                ← Document root público
│   ├── css/               ← style.css (paleta corporativa)
│   ├── fonts/
│   └── js/                ← Filtros y validaciones del cliente
├── tests/                 ← Pruebas PHPUnit
│   ├── Unit/
│   └── Utils/
├── docs/                  ← Documentación del proyecto (este directorio)
├── composer.json
├── docker-compose.yml
├── Dockerfile
├── index.php              ← Front controller
├── .htaccess              ← Reescritura de URLs hacia index.php
├── php.ini
└── phpunit.xml
```

## Despliegue con Docker

`docker-compose.yml` levanta dos contenedores:

| Servicio | Imagen           | Puerto host | Función            |
|----------|------------------|-------------|--------------------|
| php      | PHP 8.2 + Apache | 8080        | Aplicación web     |
| mysql    | MySQL 8.0        | 3309 → 3306 | Base de datos      |

La administración de la base de datos se hace con **MySQL Workbench** conectándose a `localhost:3309` (usuario `root` / `root1234` o `nomina` / `1234`).

`Dockerfile` parte de `php:8.2-apache` e instala extensiones: `gd`, `pdo`, `pdo_mysql`, `intl`, `zip`. Habilita `mod_rewrite` para Apache.

## Front Controller y Routing

`public/index.php` actúa como front controller único. Todas las rutas se reescriben vía `.htaccess` y se despachan con **Steampixel Router**:

```
GET  /                     → DashboardController::index
GET  /login                → AuthController::loginForm
POST /login                → AuthController::login
GET  /empleados            → EmpleadoController::index
GET  /empleados/nuevo      → EmpleadoController::create
POST /empleados            → EmpleadoController::store
GET  /nomina/periodos      → PeriodoController::index
POST /nomina/procesar/{id} → NominaController::procesar
GET  /reportes/nomina      → ReporteController::nomina
```

## Manejo de Sesiones y Seguridad

- Sesiones nativas PHP almacenadas server-side.
- CSRF token en formularios POST.
- Contraseñas con `password_hash()` (BCRYPT).
- Doctrine ORM previene SQL injection automáticamente vía DQL/QueryBuilder.
- Escape HTML en vistas mediante helper `e()`.

## Configuración por Entorno

`vlucas/phpdotenv` carga `.env` con:
```
DB_HOST=db
DB_PORT=3306
DB_NAME=nomina_express
DB_USER=nomina
DB_PASS=...
APP_ENV=development
APP_DEBUG=true
```

## Comandos CLI

Vía `cli/` y Doctrine:
```
php cli/console.php orm:schema-tool:create   # Crear schema desde Entities
php cli/console.php orm:schema-tool:update   # Sincronizar cambios
php cli/console.php seed:run                  # Cargar datos de prueba
```
