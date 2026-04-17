<!doctype html>
<html lang="es">
<head>
    <title>Iniciar sesión | Nomina Express</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="ne-login-bg">
        <div class="ne-login-card">
            <div class="ne-brand">
                <span class="ne-brand__icon"><i class="bi bi-cash-coin"></i></span>
                <div>
                    <div class="ne-brand__title">Nomina Express</div>
                    <div class="ne-brand__subtitle">TechSoft RD, S.R.L.</div>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="ne-alert ne-alert--danger"><?= htmlspecialchars($message ?? 'Error') ?></div>
            <?php endif; ?>

            <form method="post" action="/login">
                <div class="ne-form-group">
                    <label class="ne-form-label">Usuario</label>
                    <input type="text" name="username" class="ne-form-control" autofocus required>
                </div>

                <div class="ne-form-group">
                    <label class="ne-form-label">Contraseña</label>
                    <input type="password" name="password" class="ne-form-control" required>
                </div>

                <button type="submit" class="ne-btn ne-btn--primary" style="width:100%; justify-content:center; padding: 12px;">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                </button>
            </form>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--ne-border); font-size: 12px; color: var(--ne-text-muted);">
                <strong>Usuarios de prueba:</strong><br>
                <code>admin</code> / <code>rrhh</code> / <code>consulta</code> · clave: <code>contraseña</code>
            </div>
        </div>
    </div>
</body>
</html>
