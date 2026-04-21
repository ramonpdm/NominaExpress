# Nomina Express

> **Sistema disponible en:** https://nominaexpress.infinityfree.me/

Nomina Express es un sistema integral de gestión de nóminas desarrollado en PHP 8.2, utilizando Doctrine ORM y una arquitectura limpia basada en controladores, repositorios y entidades.

---

## Acerca del Sistema

Nomina Express proporciona una solución completa para la administración de nóminas en pequeñas y medianas empresas. El sistema incluye gestión de empleados, períodos de nómina inteligentes, cálculo automático de salarios y una interfaz moderna y dinámica.

### Características Principales

- **Gestión de Empleados:** Mantenimiento completo de empleados, salarios, departamentos y cargos.
- **Períodos de Nómina Inteligentes:** Generación automática de fechas y nombres para períodos quincenales, mensuales y semanales mediante JS.
- **Procesamiento de Nómina:** Cálculo automático de ingresos, deducciones de ley y salarios netos. Validación estricta antes del cierre y pago de nómina.
- **Interfaz Moderna y Dinámica:** Tablas globales con *scroll* vertical, cabeceras fijas (*sticky headers*) y ordenamiento dinámico por columnas al hacer clic.
- **Filtros Inteligentes:** En el listado de empleados, filtros en tiempo real que combinan búsqueda de texto abierto y menús desplegables auto-generados a partir de los datos existentes.

---

## Arquitectura del Sistema

### 1. Rutas (`app/Config/Routes.php`)

Las rutas mapean las URLs a los controladores. Determinan qué código se ejecuta cuando llega una petición HTTP.

Ejemplo:

```php
Route::add('/empleados', $this->call(\App\Controllers\Frontend\EmpleadosController::class, 'index'));
```

---

### 2. Controladores (`app/Controllers/`)

Contienen la lógica de negocio. Reciben las peticiones, coordinan con los servicios y repositorios, y devuelven una vista o redirección.

Ejemplo:

```php
// app/Controllers/Frontend/PeriodosController.php
public function cerrar(int $id): string { ... }
```

---

### 3. Entidades y Enums (`app/Entities/`, `app/Enums/`)

Representan el modelo de la base de datos usando atributos de Doctrine ORM.

Ejemplos de entidades:

```php
#[ORM\Entity]
class Empleado { ... }
```

Enums: Centralizan estados fijos.

```php
enum EstadoPeriodo: string { case ABIERTO = 'abierto'; }
```

---

### 4. Servicios (`app/Services/`)

Contienen lógica compleja y cálculos pesados para mantener los controladores limpios.

Ejemplo de procesamiento masivo:

```php
// app/Services/NominaCalculator.php
$calculator->procesarPeriodo($periodo);
```

---

### 5. Vistas (`app/Views/`)

Plantillas HTML renderizadas en el servidor con PHP.

- Contiene componentes modulares (`inc/header.php`) y vistas específicas.
- **JavaScript Centralizado:** Las mejoras visuales de tablas (ordenamiento, *scroll*, filtros en cliente) están centralizadas en `public/js/app.js`.

---

## Cómo Extender Funcionalidades

- **Añadir una nueva vista/ruta:**
  1. Registra la URL en `Routes.php`
  2. Crea el método en un controlador
  3. Diseña la plantilla en `Views/`
- **Modificar la Base de Datos:**
  Actualiza los atributos PHP dentro de `Entities/` y luego ejecuta:
  ```bash
  ./cli db:update
  ```
- **Añadir reglas de negocio:**
  Si vas a crear cálculos complejos de deducciones o reportes personalizados, añade la lógica en `Services/`.

---

## Guía de Despliegue

### Requisitos

- PHP 8.2
- MySQL 8.0
- Composer
- Docker

### Instalación

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