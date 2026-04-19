# Nomina Express

Nomina Express es un sistema integral de gestión de nóminas desarrollado en PHP 8.4, utilizando Doctrine ORM y una arquitectura limpia basada en controladores, repositorios y entidades.

## Requisitos

- PHP 8.4
- MySQL 8.0
- Composer
- Docker

## Instalación

1. Instalar Docker.
2. Ejecutar el siguiente comando para levantar el entorno de contenedores:

```bash
docker compose up -d
``` 

3. Instalar las dependencias del proyecto:

```bash
composer install
```

4. Crear un archivo de configuración de entorno `.env` en la raíz del proyecto y agregar las variables de conexión:

```
ENVIRONMENT=development

DB_DRIVER=pdo_mysql
DB_HOST=mysql
DB_PORT=3306
DB_NAME=nomina_express
DB_USER=nomina
DB_PASS=1234
```

5. Ejecutar el siguiente comando para actualizar el esquema de la base de datos e inyectar datos de prueba (seeders):

```bash
./cli db:update
```

6. Acceder a Nomina Express en tu navegador (el puerto puede variar según tu configuración de Docker, típicamente `http://localhost` o `http://127.0.0.1:3002`):

```
http://127.0.0.1:3002
```

## Características Principales

- **Gestión de Empleados:** Mantenimiento completo de empleados, salarios, departamentos y cargos.
- **Períodos de Nómina Inteligentes:** Generación automática de fechas y nombres para períodos quincenales, mensuales y semanales mediante JS.
- **Procesamiento de Nómina:** Cálculo automático de ingresos, deducciones de ley y salarios netos. Validación estricta antes del cierre y pago de nómina.
- **Interfaz Moderna y Dinámica:** Tablas globales con *scroll* vertical, cabeceras fijas (*sticky headers*) y ordenamiento dinámico por columnas al hacer clic.
- **Filtros Inteligentes:** En el listado de empleados, filtros en tiempo real que combinan búsqueda de texto abierto y menús desplegables auto-generados a partir de los datos existentes.

## Arquitectura del Sistema

A continuación se explica la estructura del proyecto y dónde extender sus funcionalidades.

### 1. Rutas (`app/Config/Routes.php`)
Las rutas mapean las URLs a los controladores. Determinan qué código se ejecuta cuando llega una petición HTTP.
- Ejemplo: `Route::add('/empleados', $this->call(\App\Controllers\Frontend\EmpleadosController::class, 'index'));`

### 2. Controladores (`app/Controllers/`)
Contienen la lógica de negocio. Reciben las peticiones, coordinan con los servicios y repositorios, y devuelven una vista o redirección.
- Ejemplo: `app/Controllers/Frontend/PeriodosController.php` maneja la apertura, validación y transición a PAGADO de las nóminas.

### 3. Entidades y Enums (`app/Entities/`, `app/Enums/`)
Representan el modelo de la base de datos usando atributos de Doctrine ORM (`#[ORM\Column]`, etc.).
- Ejemplos: `Empleado.php`, `PeriodoNomina.php`, `Nomina.php`.
- Enums: Centralizan estados fijos (ej: `EstadoPeriodo::ABIERTO`, `TipoPeriodo::QUINCENAL`).

### 4. Servicios (`app/Services/`)
Contienen lógica compleja y cálculos pesados para mantener los controladores limpios.
- Ejemplo: `NominaCalculator.php` procesa los cálculos de deducciones de forma masiva o individual.

### 5. Vistas (`app/Views/`)
Plantillas HTML renderizadas en el servidor con PHP. 
- Contiene componentes modulares (`inc/header.php`) y vistas específicas.
- **JavaScript Centralizado:** Las mejoras visuales de tablas (ordenamiento, *scroll*, filtros en cliente) están centralizadas en `public/js/app.js`.

## Cómo Extender Funcionalidades

- **Añadir una nueva vista/ruta:** Registra la URL en `Routes.php`, crea el método en un controlador, y diseña la plantilla en `Views/`.
- **Modificar la Base de Datos:** Actualiza los atributos PHP en los archivos dentro de `Entities/` y luego ejecuta `./cli db:update`.
- **Añadir reglas de negocio:** Si vas a crear cálculos complejos de deducciones o reportes personalizados, añade la lógica en `Services/`.