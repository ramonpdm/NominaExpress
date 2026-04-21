# Algoritmos Aplicados en Nomina Express

> **Objetivo académico:** demostrar dominio práctico de al menos uno de los algoritmos enseñados en la asignatura **INF3220 — Algoritmos Computacionales** (Prof. Luis Alberto Adames). Para enriquecer el proyecto, implementamos varios algoritmos de distintas categorías de la materia, justificando su uso real dentro del sistema.

---

## Catálogo de Algoritmos Implementados

| # | Algoritmo                         | Unidad | Categoría                      | Dónde se usa en el sistema                                                  |
|---|-----------------------------------|--------|--------------------------------|------------------------------------------------------------------------------|
| 1 | **QuickSort**                     | 4      | Ordenación rápida              | Ordenar reportes de empleados por nombre, salario o fecha de ingreso        |
| 2 | **Búsqueda Binaria**              | 3      | Búsqueda eficiente             | Localizar empleado por ID o cédula en arreglo ordenado en memoria           |
| 3 | **Suma Iterativa (acumulador)**   | 5      | Aritmético iterativo           | Totales de salarios brutos, deducciones y netos durante el cálculo masivo   |
| 4 | **Media Aritmética**              | 5      | Aritmético                     | Promedio salarial por departamento en reportes gerenciales                  |
| 5 | **Recursión**                     | 2      | Técnicas de diseño             | Cálculo de totales jerárquicos por departamento (caso de uso de organigrama)|
| 6 | **Hashing seguro (bcrypt/Argon2)**| 6      | Criptografía / Seguridad       | Almacenamiento irreversible de contraseñas de usuarios                      |

---

## 1. QuickSort — Ordenación Rápida

**Justificación:** Aunque MySQL ya soporta `ORDER BY`, en los reportes gerenciales recuperamos colecciones de empleados a memoria PHP para luego transformarlas (cálculos, agrupaciones, formato). Implementamos un QuickSort propio para ordenar dichas colecciones por cualquier campo en memoria, demostrando el algoritmo "divide y vencerás" estudiado en la unidad 4.

**Caso de uso real:** Ordenar la planilla de empleados activos por nombre, salario o fecha de ingreso antes de pasarla a la vista.

**Complejidad:**
- Mejor caso: **Θ(n log n)** — partición balanceada.
- Caso promedio: **Θ(n log n)**.
- Peor caso: **O(n²)** — pivote mal elegido en arreglo ya ordenado.
- Mitigación: pivote por mediana o aleatorio.

**Ubicación prevista:** `app/Algorithms/QuickSort.php` (clase utilitaria genérica con callback de comparación).

---

## 2. Búsqueda Binaria

**Justificación:** Durante el procesamiento masivo de la nómina cargamos en memoria un arreglo de empleados ordenado por ID. Cada vez que necesitamos resolver el empleado de una novedad (por ID o cédula) usamos búsqueda binaria en lugar de recorrido lineal.

**Caso de uso real:** Resolver `empleado_id` de cada línea de novedades dentro del proceso de cálculo de nómina, evitando consultas repetidas a la base de datos.

**Complejidad:**
- Mejor caso: **Ω(1)** — el elemento está en la posición media.
- Caso promedio y peor: **Θ(log n)**.
- Requisito: arreglo previamente ordenado.

**Ubicación prevista:** `app/Algorithms/BinarySearch.php`.

---

## 3. Suma Iterativa (Algoritmo Acumulador)

**Justificación:** El cálculo de la nómina necesita acumular tres totales (ingresos, deducciones, neto) recorriendo los detalles de cada empleado y los conceptos del período. Es el algoritmo aritmético iterativo clásico de la unidad 5.

**Caso de uso real:**
```
totalIngresos    ← 0
totalDeducciones ← 0
PARA CADA detalle EN nominaDetalle:
    SI detalle.tipo = 'ingreso'    → totalIngresos    += detalle.monto
    SI detalle.tipo = 'deduccion'  → totalDeducciones += detalle.monto
salarioNeto = totalIngresos − totalDeducciones
```

**Complejidad:** **Θ(n)** lineal en número de detalles.

**Ubicación prevista:** Embebido en `app/Services/NominaCalculator.php`.

---

## 4. Media Aritmética

**Justificación:** Reportes gerenciales requieren conocer el salario promedio por departamento, por cargo y a nivel global. Si bien podría resolverse en SQL con `AVG()`, lo implementamos en PHP para mostrar el algoritmo aritmético de la materia y así desacoplarlo del motor de base de datos.

**Caso de uso real:** "Salario promedio del departamento de Tecnología de Información".

**Complejidad:** **Θ(n)** — un solo recorrido para sumar y dividir entre la cantidad.

**Ubicación prevista:** `app/Algorithms/Estadistica.php` (con `media()`, `minimo()`, `maximo()`).

---

## 5. Recursión

**Justificación:** Aunque la estructura inicial de departamentos de TechSoft RD es plana, modelamos los departamentos con un campo `padre_id` (autoreferencia opcional) para soportar subdepartamentos a futuro. La función `totalNominaPorDepartamento(deptoId)` baja recursivamente sumando los totales de cada subdepartamento — patrón clásico de recursión sobre estructura jerárquica (unidad 2).

**Caso de uso real:** Sumar el costo total de la nómina de "Tecnología de Información" incluyendo cualquier subequipo que cuelgue debajo.

**Complejidad:** **Θ(n)** donde n es el número de nodos del subárbol.

**Ubicación prevista:** `app/Services/OrganigramaService.php`.

---

## 6. Hashing Seguro (bcrypt / Argon2)

**Justificación:** Las contraseñas de los usuarios (admin, RRHH, consulta) jamás se almacenan en texto plano. Usamos las funciones nativas de PHP `password_hash()` con el algoritmo `PASSWORD_BCRYPT` (o `PASSWORD_ARGON2ID` cuando esté disponible), que internamente generan un *salt* aleatorio. Esto cubre los temas de la unidad 6: HASH irreversibles + algoritmos aleatorios para "salting".

> **Nota académica:** El temario menciona MD5 y SHA1 como ejemplos clásicos de funciones HASH, pero ambos están comprometidos para almacenamiento de contraseñas. Documentamos la limitación y elegimos bcrypt/Argon2 como evolución segura del mismo concepto. Esta decisión se discute en la sección de "experiencia adquirida" del informe.

**Caso de uso real:** Tabla `usuarios.password_hash` almacena el hash; al iniciar sesión se valida con `password_verify()`.

**Complejidad de cómputo:** intencionalmente lenta — costo configurable para resistir fuerza bruta.

**Ubicación prevista:** `app/Services/AuthService.php`.

---

## Análisis Asintótico Global

El proceso completo de procesar la nómina de un período tiene complejidad:

```
T(n, m) = O(n log n) + O(n × m)
        ≈ O(n × m)
```
donde:
- `n` = cantidad de empleados activos.
- `m` = cantidad de conceptos obligatorios.

Para TechSoft RD (n ≈ 52, m ≈ 5): el sistema es prácticamente instantáneo y escala holgadamente a miles de empleados.

## Análisis de Casos del Cálculo de Nómina
- **Mejor caso (Ω):** todos los empleados están activos sin novedades → un solo recorrido lineal.
- **Caso promedio (Θ):** porcentaje normal de novedades (horas extras, bonos) → recorrido lineal + búsquedas binarias O(log n) por novedad.
- **Peor caso (O):** todos los empleados con múltiples novedades y QuickSort cae en O(n²) por pivote degenerado → mitigado eligiendo pivote aleatorio.
