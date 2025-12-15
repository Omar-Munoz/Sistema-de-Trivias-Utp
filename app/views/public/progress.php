<?php
// Espera $u desde el controlador o desde layout.
// Si no llega, intenta sesión:
if (!isset($u)) {
  $u = \App\Core\Session::get('user');
}
if (!$u) {
  // Si alguien entra sin login, mejor redirigir (por seguridad)
  \App\Core\Response::redirect('/login');
}

$defaultAvatarUrl = $baseUrl . '/assets/img/default-avatar.png';
$activeAvatarUrl = null;

try {
  $pdo = \App\Core\Database::pdo();
  $st = $pdo->prepare("SELECT filename FROM avatars WHERE user_id=? AND is_active=1 LIMIT 1");
  $st->execute([(int)$u['id']]);
  $row = $st->fetch();
  if ($row && !empty($row['filename'])) {
    $activeAvatarUrl = $baseUrl . '/uploads/avatars/' . $row['filename'];
  }
} catch (\Throwable $e) {
  $activeAvatarUrl = null;
}

if (!$activeAvatarUrl) {
  $activeAvatarUrl = $defaultAvatarUrl;
}

// Si tu controlador pasa progreso, úsalo; si no, evita errores:
$progress = $progress ?? [];
?>

<?php ob_start(); ?>

<h2>Mi Progreso</h2>

<div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
  <a class="btn gray" href="<?= $baseUrl ?>/">⬅ Regresar</a>
  <a class="btn gray" href="<?= $baseUrl ?>/play">Jugar</a>
  <a class="btn" href="<?= $baseUrl ?>/logout">Logout</a>
</div>

<br>

<!-- PERFIL + AVATAR GRANDE -->
<div class="card" style="display:flex; gap:18px; align-items:center; flex-wrap:wrap;">
  <div style="width:170px; height:170px; border-radius:18px; overflow:hidden; border:2px solid #ddd; background:#fafafa;">
    <img src="<?= htmlspecialchars($activeAvatarUrl) ?>"
         alt="Avatar"
         style="width:100%; height:100%; object-fit:cover;">
  </div>

  <div style="flex:1; min-width:280px;">
    <h3 style="margin:0 0 6px;">Perfil</h3>
    <div style="opacity:.85;">
      <div><strong>Nombre:</strong> <?= htmlspecialchars($u['name'] ?? '-') ?></div>
      <div><strong>Apodo:</strong> <?= htmlspecialchars($u['nickname'] ?? '-') ?></div>
      <div><strong>Correo:</strong> <?= htmlspecialchars($u['email'] ?? '-') ?></div>
      <div><strong>Rol:</strong> <?= htmlspecialchars($u['role'] ?? '-') ?></div>
    </div>

    <hr style="margin:12px 0; opacity:.3;">

    <h3 style="margin:0 0 6px;">Mi Avatar</h3>

    <!-- Subir / cambiar -->
    <form action="<?= $baseUrl ?>/player/avatar/upload" method="POST" enctype="multipart/form-data">
      <input type="file" name="avatar" accept="image/*" required>
      <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn" type="submit">Subir / Cambiar Avatar</button>
      </div>
    </form>

    <!-- Quitar avatar (queda default) -->
    <form action="<?= $baseUrl ?>/player/avatar/clear" method="POST" style="margin-top:10px;">
      <button class="btn gray" type="submit"
              onclick="return confirm('¿Quitar el avatar y usar el predeterminado?');">
        Quitar Avatar
      </button>
    </form>

    <p style="font-size:12px; opacity:.75; margin-top:10px;">
      Si quitas el avatar, se mostrará una imagen por defecto.
    </p>
  </div>
</div>

<br>

<!-- RESUMEN (si tienes datos de progreso, los muestra; si no, no rompe) -->
<div class="card">
  <h3 style="margin-top:0;">Resumen</h3>

  <?php if (empty($progress)): ?>
    <p style="opacity:.8;">Aún no tienes avances registrados. Presiona <strong>Jugar</strong> para comenzar.</p>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>Tema</th>
          <th>Nivel</th>
          <th>Puntos</th>
          <th>%</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($progress as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['topic'] ?? '-') ?></td>
            <td><?= htmlspecialchars($p['level'] ?? '-') ?></td>
            <td><?= (int)($p['total_points'] ?? 0) ?></td>
            <td><?= (int)($p['percent_complete'] ?? 0) ?>%</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
    <a class="btn gray" href="<?= $baseUrl ?>/my-prizes">Ver mis premios</a>
    <a class="btn gray" href="<?= $baseUrl ?>/">Regresar</a>
  </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/public.php'; ?>
