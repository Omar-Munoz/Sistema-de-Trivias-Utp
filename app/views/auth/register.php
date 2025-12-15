<?php ob_start(); ?>
<div class="card">
    <h2>Registro Público</h2>
    <form method="POST" action="<?= $baseUrl ?>/register">
        <div class="row">
            <div>
                <label>Correo</label>
                <input name="email" type="email" required>
            </div>
            <div>
                <label>Nombre</label>
                <input name="name" type="text" required>
            </div>
        </div>
        <div class="row">
            <div>
                <label>Apodo</label>
                <input name="nickname" type="text" required>
            </div>
            <div>
                <label>Contraseña (min 6)</label>
                <input name="password" type="password" required>
            </div>
        </div>
        <button class="btn" type="submit">Crear cuenta</button>
    </form>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/public.php'; ?>