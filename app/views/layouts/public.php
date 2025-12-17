<?php
declare(strict_types=1);

use App\Core\Session;

// baseUrl
if (!isset($baseUrl)) {
  $config = require __DIR__ . '/../../config/config.php';
  $baseUrl = rtrim($config['app']['base_url'] ?? '', '');
}

// user (si el controlador no lo pasó)
if (!isset($u)) {
  $u = Session::get('user'); // null si no está logueado
}

// Flash messages
$flashOk  = Session::flash('ok');
$flashErr = Session::flash('err');

// Avatar fijo en esquina (si hay sesión)
$defaultAvatarUrl = $baseUrl . '/assets/img/default-avatar.png';
$avatarUrl = null;

if (!empty($u['id'] ?? null)) {
  try {
    $pdo = \App\Core\Database::pdo();
    $st = $pdo->prepare("SELECT filename FROM avatars WHERE user_id=? AND is_active=1 LIMIT 1");
    $st->execute([(int)$u['id']]);
    $av = $st->fetch();

    if ($av && !empty($av['filename'])) {
      // ✅ OJO: NO /public/uploads porque base_url ya incluye /public
      $avatarUrl = $baseUrl . '/uploads/avatars/' . $av['filename'];
    }
  } catch (\Throwable $e) {
    $avatarUrl = null;
  }
}

// Si está logueado y no tiene avatar activo -> default
if (!empty($u['id'] ?? null) && !$avatarUrl) {
  $avatarUrl = $defaultAvatarUrl;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trivias UTP</title>

  <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/css/app.css">
  <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/css/theme.css">
  <style>
    .container{max-width:1000px;margin:0 auto;padding:18px;}
    .topbar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;}
    .nav{display:flex;gap:8px;flex-wrap:wrap;align-items:center;}
    .msg{padding:10px 12px;border-radius:10px;margin:10px 0;}
    .msg.ok{background:#e9f7ef;border:1px solid #b7e1c2;}
    .msg.err{background:#fdecea;border:1px solid #f5b5ad;}
  </style>
</head>
<body>

<?php if (!empty($u['id'] ?? null) && $avatarUrl): ?>
  <!-- Avatar fijo en esquina -->
  <div style="position:fixed; top:12px; right:12px; z-index:9999;">
    <img
      src="<?= htmlspecialchars($avatarUrl) ?>"
      alt="Avatar"
      style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid #ddd;box-shadow:0 2px 10px rgba(0,0,0,.12);background:#fff;"
    >
  </div>
<?php endif; ?>

<div class="container">

  <!-- Header / Navbar -->
  <div class="card topbar">
    <div>
      <strong>Trivias UTP</strong>
      <div style="font-size:12px; opacity:.75;">Sistema de Trivias</div>
    </div>

    <div class="nav">
      <a class="btn gray" href="<?= htmlspecialchars($baseUrl) ?>/">Inicio</a>

      <?php if (!$u): ?>
        <a class="btn gray" href="<?= htmlspecialchars($baseUrl) ?>/register">Registro</a>
        <a class="btn" href="<?= htmlspecialchars($baseUrl) ?>/login">Login</a>
      <?php else: ?>
        <a class="btn gray" href="<?= htmlspecialchars($baseUrl) ?>/play">Jugar</a>
        <a class="btn gray" href="<?= htmlspecialchars($baseUrl) ?>/progress">Mi Progreso</a>
        <a class="btn gray" href="<?= htmlspecialchars($baseUrl) ?>/my-prizes">Mis Premios</a>
        <a class="btn gray" href="<?= $baseUrl ?>/ranking">Ranking</a>


        <?php if (($u['role'] ?? '') === 'admin' || ($u['role'] ?? '') === 'operator'): ?>
          <a class="btn gray" href="<?= htmlspecialchars($baseUrl) ?>/admin">Panel Admin</a>
        <?php endif; ?>

        <span style="font-size:13px; opacity:.85;">
          <?= htmlspecialchars($u['nickname'] ?? $u['name'] ?? 'Usuario') ?>
        </span>

        <!-- ✅ Logout visible siempre que esté logueado -->
        <a class="btn" href="<?= htmlspecialchars($baseUrl) ?>/logout">Logout</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Flash messages -->
  <?php if ($flashOk): ?>
    <div class="msg ok"><?= htmlspecialchars($flashOk) ?></div>
  <?php endif; ?>

  <?php if ($flashErr): ?>
    <div class="msg err"><?= htmlspecialchars($flashErr) ?></div>
  <?php endif; ?>

  <!-- Contenido -->
  <?= $content ?? '' ?>

  <div style="padding:18px 0; font-size:12px; opacity:.7; text-align:center;">
    Universidad Tecnológica de Panamá • Ingeniería Web
  </div>

</div>
</body>
</html>
