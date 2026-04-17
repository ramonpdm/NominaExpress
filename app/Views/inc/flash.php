<?php if (isset($_SESSION['flash'])): ?>
    <div class="ne-alert ne-alert--<?= htmlspecialchars($_SESSION['flash']['tipo']) ?>">
        <?= htmlspecialchars($_SESSION['flash']['msg']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
