# Modelo de Datos

El sistema usa **MySQL 8.0** mapeado mediante **Doctrine ORM 3** con entidades anotadas vía atributos PHP 8. Son **8 entidades principales** con sus relaciones de uno a muchos.

## Diagrama Conceptual (ER simplificado)

```
        ┌──────────────┐        ┌──────────┐
        │ departamentos│◄───────│  cargos  │
        └──────┬───────┘        └────┬─────┘
               │                     │
               │ 1                 1 │
               ▼ N                 N ▼
              ┌──────────────────────────┐
              │        empleados          │
              └────────────┬──────────────┘
                           │ 1
                           │
                           ▼ N
        ┌──────────────────────────────────┐
        │             nomina               │◄────┐
        └────────────┬─────────────────────┘     │
                     │ 1                         │ N
                     ▼ N                         │ 1
              ┌──────────────────┐    ┌─────────────────────┐
              │ nomina_detalle   │───►│ conceptos_nomina    │
              └──────────────────┘  N    └─────────────────────┘
                                                  ▲
                                                  │ 1
                     ┌────────────────────────────┘
                     │
              ┌─────────────────────┐
              │  periodos_nomina    │ ←── nomina pertenece a un periodo
              └─────────────────────┘

              ┌─────────────────────┐
              │      usuarios       │ (independiente, sólo para login)
              └─────────────────────┘
```

## Entidades

### 1. `departamentos`
| Campo       | Tipo                 | Notas                                      |
|-------------|----------------------|--------------------------------------------|
| id          | INT (PK, AI)         |                                            |
| nombre      | VARCHAR(100)         | UNIQUE                                     |
| descripcion | VARCHAR(255)         | Nullable                                   |
| padre_id    | INT (FK → departamentos.id) | Nullable, soporta jerarquía/subdeptos |
| estado      | ENUM('activo','inactivo') |                                       |
| created_at  | DATETIME             |                                            |

### 2. `cargos`
| Campo                 | Tipo                | Notas                       |
|-----------------------|---------------------|-----------------------------|
| id                    | INT (PK, AI)        |                             |
| nombre                | VARCHAR(100)        |                             |
| salario_base_sugerido | DECIMAL(12,2)       | Para autocompletado en form |
| departamento_id       | INT (FK)            |                             |
| estado                | ENUM('activo','inactivo') |                       |

### 3. `empleados`
| Campo            | Tipo                                              | Notas                  |
|------------------|---------------------------------------------------|------------------------|
| id               | INT (PK, AI)                                      |                        |
| cedula           | VARCHAR(20)                                       | UNIQUE                 |
| nombres          | VARCHAR(100)                                      |                        |
| apellidos        | VARCHAR(100)                                      |                        |
| fecha_nacimiento | DATE                                              |                        |
| sexo             | ENUM('M','F')                                     |                        |
| telefono         | VARCHAR(20)                                       | Nullable               |
| email            | VARCHAR(150)                                      | Nullable               |
| direccion        | VARCHAR(255)                                      | Nullable               |
| departamento_id  | INT (FK)                                          |                        |
| cargo_id         | INT (FK)                                          |                        |
| salario          | DECIMAL(12,2)                                     | Salario mensual bruto  |
| fecha_ingreso    | DATE                                              |                        |
| tipo_contrato    | ENUM('indefinido','temporal','pasantia')          |                        |
| estado           | ENUM('activo','inactivo','suspendido')            |                        |
| created_at       | DATETIME                                          |                        |
| updated_at       | DATETIME                                          |                        |

### 4. `periodos_nomina`
| Campo           | Tipo                                   | Notas                |
|-----------------|----------------------------------------|----------------------|
| id              | INT (PK, AI)                           |                      |
| nombre          | VARCHAR(100)                           | Ej: "1ra Quincena Abril 2026" |
| tipo            | ENUM('semanal','quincenal','mensual')  | Quincenal por default |
| fecha_inicio    | DATE                                   |                      |
| fecha_fin       | DATE                                   |                      |
| fecha_pago      | DATE                                   |                      |
| estado          | ENUM('abierto','cerrado','pagado')     |                      |

### 5. `conceptos_nomina`
Catálogo configurable de tipos de ingresos y deducciones.

| Campo          | Tipo                                | Notas                            |
|----------------|-------------------------------------|----------------------------------|
| id             | INT (PK, AI)                        |                                  |
| codigo         | VARCHAR(20)                         | UNIQUE — ej: 'AFP', 'ARS', 'ISR' |
| nombre         | VARCHAR(100)                        |                                  |
| tipo           | ENUM('ingreso','deduccion')         |                                  |
| calculo        | ENUM('porcentaje','monto_fijo','formula') |                            |
| valor          | DECIMAL(10,4)                       | Porcentaje o monto base          |
| obligatorio    | TINYINT(1)                          | 1 = se aplica a todos            |
| estado         | ENUM('activo','inactivo')           |                                  |

### 6. `nomina` (cabecera)
Una fila por empleado por período procesado.

| Campo             | Tipo          | Notas                  |
|-------------------|---------------|------------------------|
| id                | INT (PK, AI)  |                        |
| empleado_id       | INT (FK)      |                        |
| periodo_id        | INT (FK)      |                        |
| salario_base      | DECIMAL(12,2) |                        |
| total_ingresos    | DECIMAL(12,2) |                        |
| total_deducciones | DECIMAL(12,2) |                        |
| salario_neto      | DECIMAL(12,2) |                        |
| fecha_calculo     | DATETIME      |                        |
| estado            | ENUM('calculada','pagada','anulada') |     |

UNIQUE (empleado_id, periodo_id) — un empleado solo puede tener una nómina por período.

### 7. `nomina_detalle`
Líneas individuales de cada nómina.

| Campo              | Tipo                                 | Notas |
|--------------------|--------------------------------------|-------|
| id                 | INT (PK, AI)                         |       |
| nomina_id          | INT (FK)                             |       |
| concepto_id        | INT (FK)                             |       |
| tipo               | ENUM('ingreso','deduccion')          |       |
| monto              | DECIMAL(12,2)                        |       |
| base_calculo       | DECIMAL(12,2)                        | Sobre qué se calculó |
| porcentaje_aplicado| DECIMAL(10,4)                        | Nullable |

### 8. `usuarios`
Para autenticación (independiente del expediente del empleado).

| Campo         | Tipo                                  | Notas                         |
|---------------|---------------------------------------|-------------------------------|
| id            | INT (PK, AI)                          |                               |
| username      | VARCHAR(50)                           | UNIQUE                        |
| password_hash | VARCHAR(255)                          | Bcrypt/Argon2 (ver algoritmos)|
| nombre        | VARCHAR(100)                          |                               |
| email         | VARCHAR(150)                          |                               |
| rol           | ENUM('admin','rrhh','consulta')       |                               |
| estado        | ENUM('activo','inactivo')             |                               |
| ultimo_acceso | DATETIME                              | Nullable                      |

## Variables Principales (resumen del documento)

| Variable      | Tipo SQL       | Entidad   | Descripción              |
|---------------|----------------|-----------|--------------------------|
| id            | INT (PK)       | Empleado  | Identificador único      |
| cedula        | VARCHAR(20)    | Empleado  | Cédula de identidad      |
| nombres       | VARCHAR(100)   | Empleado  | Nombres del empleado     |
| salario       | DECIMAL(12,2)  | Empleado  | Salario mensual bruto    |
| salario_neto  | DECIMAL(12,2)  | Nomina    | Salario tras deducciones |
| valor (AFP)   | DECIMAL(10,4)  | Concepto  | 2.87%                    |
| valor (ARS)   | DECIMAL(10,4)  | Concepto  | 3.04%                    |
| fecha_ingreso | DATE           | Empleado  | Fecha de contratación    |
| estado        | ENUM           | Múltiples | Estado del registro      |

## Datos Iniciales (Seeders)
Al levantar el contenedor por primera vez se insertan:
- 6 departamentos de TechSoft RD.
- ~15 cargos típicos (Desarrollador Sr, Desarrollador Jr, Analista, Gerente, etc.).
- 3 conceptos obligatorios: AFP, ARS, ISR (ya parametrizados con sus porcentajes).
- 52 empleados de prueba distribuidos en los departamentos.
- 3 usuarios: `admin`, `rrhh`, `consulta` (todos con contraseña `contraseña` hasheada).
- 1 período abierto de muestra.

## Integridad Referencial
- Llaves foráneas con `ON DELETE RESTRICT` para datos críticos (no se puede borrar un departamento con empleados activos).
- Borrado lógico mediante el campo `estado`. Nada se elimina físicamente — el sistema mantiene historial completo.

## Verificación del Algoritmo (caso de prueba documentado)
Empleado con salario RD$45,000.00:

| Concepto       | Base       | %/Monto         | Resultado     |
|----------------|------------|-----------------|---------------|
| Salario Base   | —          | —               | RD$45,000.00  |
| AFP            | 45,000.00  | 2.87%           | -RD$1,291.50  |
| ARS            | 45,000.00  | 3.04%           | -RD$1,368.00  |
| ISR mensual    | 45,000.00  | Tabla progresiva| -RD$548.50    |
| **Salario Neto**| —         | —               | **RD$41,792.00** |
