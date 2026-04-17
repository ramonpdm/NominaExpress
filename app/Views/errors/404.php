<?php include APP_VIEWS_DIR . '/inc/header.php'; ?>

<div style="text-align: center; padding: 80px 20px;">
    <?php if (isset($exception)): ?>
        <h1 style="font-size: 64px; color: var(--ne-danger);">Error</h1>
        <p class="text-muted"><?= htmlspecialchars($exception->getMessage()) ?></p>
        <?php if (($_ENV['ENVIRONMENT'] ?? '') === 'development'): ?>
            <pre style="text-align: left; max-width: 900px; margin: 20px auto; background: #fff; padding: 16px; border-radius: 8px; overflow: auto; font-size: 12px;"><?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
        <?php endif; ?>
    <?php else: ?>
        <h1 style="font-size: 96px; color: var(--ne-primary); margin: 0;">404</h1>
        <h3>Página no encontrada</h3>
        <p class="text-muted">La ruta que estás buscando no existe.</p>
        <a href="/" class="ne-btn ne-btn--primary"><i class="bi bi-house"></i> Volver al Dashboard</a>
    <?php endif; ?>
</div>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
