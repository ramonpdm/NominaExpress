# Sistema: Nomina Express

## Visión General
**Nomina Express** es un sistema de información web que automatiza el ciclo completo de la nómina de TechSoft RD: desde el registro de empleados y departamentos, hasta el procesamiento de períodos, generación de comprobantes individuales y reportes gerenciales.

## Módulos del Sistema

### 1. Módulo de Administración
Gestión de catálogos maestros del sistema:
- **Departamentos** — alta/edición/baja lógica.
- **Cargos** — con salario base sugerido por cargo.
- **Conceptos de Nómina** — catálogo configurable de ingresos y deducciones (porcentaje o monto fijo).

### 2. Módulo de Empleados
Mantenimiento del expediente digital del trabajador (CRUD):
- **Datos Personales:** cédula, nombres, apellidos, fecha de nacimiento, sexo, teléfono, correo, dirección.
- **Datos Laborales:** departamento, cargo (con autocompletado del salario base), salario mensual, fecha de ingreso, tipo de contrato.
- **Validaciones:** cédula única, campos obligatorios, rangos numéricos.
- **Estados:** activo, inactivo, suspendido.

### 3. Módulo de Nómina
- Creación de **períodos de nómina** (quincenal, mensual o semanal).
- **Procesamiento automático**: itera sobre todos los empleados activos, calcula salario bruto, deducciones (AFP, ARS, ISR) y salario neto, persiste cabecera + detalles.
- Procesamiento individual o masivo.
- **Estados de período:** abierto, cerrado, pagado.

### 4. Módulo de Comprobantes
- Generación de comprobantes individuales por empleado/período.
- Vista imprimible desde navegador (CSS print-friendly que oculta navegación y pie de página).
- Desglose paralelo de ingresos y deducciones; salario neto resaltado.

### 5. Módulo de Reportes Gerenciales
- **Reporte de Nómina por Período** — totales acumulados al pie.
- **Planilla de Empleados** — con filtros por departamento, estado y rango salarial.
- **Promedios salariales** por departamento y cargo.

### 6. Módulo de Seguridad
- Autenticación con sesiones PHP.
- Tres roles: **admin**, **rrhh**, **consulta**.
- Contraseñas almacenadas con hashing irreversible.

## Flujo Principal de Trabajo

```
┌──────────────────────────────────────────────────────────────┐
│ 1. Login (admin/rrhh/consulta)                               │
│         ↓                                                     │
│ 2. Dashboard con KPIs y últimos registros                    │
│         ↓                                                     │
│ 3. Mantenimiento de catálogos (deptos, cargos, conceptos)    │
│         ↓                                                     │
│ 4. Registro/edición de empleados                             │
│         ↓                                                     │
│ 5. Crear período de nómina                                   │
│         ↓                                                     │
│ 6. Procesar nómina (individual o masiva)                     │
│         ↓                                                     │
│ 7. Generar comprobantes y reportes                           │
│         ↓                                                     │
│ 8. Cerrar período                                            │
└──────────────────────────────────────────────────────────────┘
```

## Pantalla Principal (Dashboard)

Cuatro tarjetas de estadísticas en tiempo real:
1. Empleados activos.
2. Total de períodos de nómina creados.
3. Períodos abiertos pendientes de procesar.
4. Total acumulado de nóminas pagadas en el historial.

Debajo, dos tablas:
- Últimos empleados registrados (nombre, departamento, fecha de ingreso).
- Períodos de nómina más recientes con sus estados.

## Identidad Visual
- **Colores corporativos:** azul marino `#1A3A5C` con acentos dorados `#E8A020`.
- **Tipografía:** Arial.
- **Diseño:** Responsivo (escritorio, tableta, móvil) usando CSS Grid y Flexbox sin frameworks.

## Alcance del Proyecto

### Dentro del alcance
- CRUD completo de empleados.
- Administración de departamentos, cargos y conceptos.
- Períodos de nómina (quincenal, mensual, semanal).
- Cálculo automático de AFP (2.87%), ARS (3.04%) e ISR según tabla progresiva dominicana.
- Comprobantes de pago individuales imprimibles.
- Reportes consolidados por período y planilla con filtros.
- Autenticación con roles.

### Fuera del alcance
- Integración con sistemas bancarios para pagos electrónicos.
- Módulo avanzado de préstamos a empleados.
- Generación automática de planilla 606 para la DGII.

## Casos de Uso de Aceptación
| Caso de Prueba                                          | Resultado Esperado                  |
|---------------------------------------------------------|-------------------------------------|
| Login con credenciales válidas                          | Redirige al dashboard               |
| Registro de empleado con cédula duplicada               | Mensaje de error                    |
| Procesar nómina para todos los empleados activos        | Calcula y guarda correctamente      |
| Ver comprobante individual                              | Desglose completo e imprimible      |
| Filtrar reporte de empleados por estado                 | Solo muestra el estado seleccionado |
| Cierre de período                                       | Cambia estado a 'cerrado'           |
