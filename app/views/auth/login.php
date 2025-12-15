<?php ob_start(); ?>
<div class="card">
    <h2>Login</h2>
    <form method="POST" action="<?= $baseUrl ?>/login">
        <div class="row">
            <div>
                <label>Correo</label>
                <input name="email" type="email" required>
            </div>
            <div>
                <label>Contraseña</label>
                <input name="password" type="password" required>
            </div>
        </div>
        <button class="btn" type="submit">Entrar</button>
    </form>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/public.php'; ?>

