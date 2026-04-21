<?php use App\Config\Auth; ?>
<!doctype html>
<html lang="es">

    <head>
        <title><?= $title ?? 'Nomina Express' ?> | Nomina Express</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

        <link rel="stylesheet" href="/public/css/style.css">

        <script type="application/javascript">
            const IS_LOGGED  = <?= Auth::isLogged() ? 'true' : 'false' ?>;
            const USER_ROL   = "<?= Auth::user()?->rol->value ?? '' ?>";
        </script>
    </head>

    <body>
        <?php if (Auth::isLogged()): ?>
        <header class="ne-navbar">
            <div class="ne-navbar__inner">
                <a href="/" class="ne-brand">
                    <div class="ne-brand__title">Nomina Express</div>
                    <div class="ne-brand__subtitle">TechSoft RD</div>
                </a>

                <nav class="ne-nav">
                    <a href="/" class="ne-nav__link"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a href="/empleados" class="ne-nav__link"><i class="bi bi-people"></i> Empleados</a>
                    <a href="/periodos" class="ne-nav__link"><i class="bi bi-calendar-week"></i> Nómina</a>

                    <div class="ne-nav__dropdown">
                        <a href="#" class="ne-nav__link"><i class="bi bi-gear"></i> Admin <i class="bi bi-chevron-down small"></i></a>
                        <div class="ne-nav__menu">
                            <a href="/departamentos">Departamentos</a>
                            <a href="/cargos">Cargos</a>
                            <a href="/conceptos">Conceptos</a>
                        </div>
                    </div>

                    <div class="ne-nav__dropdown">
                        <a href="#" class="ne-nav__link"><i class="bi bi-bar-chart-line"></i> Reportes <i class="bi bi-chevron-down small"></i></a>
                        <div class="ne-nav__menu">
                            <a href="/reportes/nomina">Nómina por Período</a>
                            <a href="/reportes/empleados">Planilla de Empleados</a>
                            <a href="/reportes/departamentos">Por Departamento</a>
                        </div>
                    </div>
                </nav>

                <div class="ne-user">
                    <div class="ne-user__info">
                        <div class="ne-user__name"><?= Auth::user()->getNombreCompleto() ?></div>
                        <div class="ne-user__rol"><?= Auth::user()->rol->label() ?></div>
                    </div>
                    <a href="/logout" class="ne-btn ne-btn--ghost" title="Cerrar sesión">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </div>
        </header>
        <?php endif; ?>

        <main class="ne-main">
