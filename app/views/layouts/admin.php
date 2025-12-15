<?php
use App\Core\Session;
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$baseUrl = $baseUrl ?? '';
$user = $_SESSION['user'] ?? null;
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Admin • Trivias UTP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS base + THEME -->
  <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/css/app.css">
  <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/datatables/datatables.min.css">
  <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/css/theme.css">


</head>
<body>

<!-- HEADER ADMIN -->
<header class="container card topbar">
  <div>
    <h1>🛠 Panel Administrativo</h1>
    <p class="muted">Gestión del sistema de trivias</p>
  </div>

  <nav class="row">
    <a class="btn gray" href="<?= $baseUrl ?>/admin">Dashboard</a>
    <a class="btn gray" href="<?= $baseUrl ?>/admin/users">Usuarios</a>
    <a class="btn gray" href="<?= $baseUrl ?>/admin/sets">Sets</a>
    <a class="btn gray" href="<?= $baseUrl ?>/admin/topics">Temas</a>
    <a class="btn gray" href="<?= $baseUrl ?>/admin/questions">Preguntas</a>
    <a class="btn gray" href="<?= $baseUrl ?>/admin/prizes">Premios</a>
    <a class="btn gray" href="<?= $baseUrl ?>/admin/reports/excel">Excel</a>
    <a class="btn red" href="<?= $baseUrl ?>/logout">Salir</a>
  </nav>
</header>



<!-- FLASH -->
<div class="container">
  <?php 
    // Usar Session::flash() para obtener los mensajes
    $flashOk  = Session::flash('ok');
    $flashErr = Session::flash('err');

    if ($flashOk): ?>
      <div class="msg ok"><?= htmlspecialchars($flashOk) ?></div>
  <?php endif; ?>

  <?php if ($flashErr): ?>
    <div class="msg err"><?= htmlspecialchars($flashErr) ?></div>
  <?php endif; ?>
</div>

<!-- CONTENIDO ADMIN -->
<main class="container">
  <?= $content ?>
</main>

<!-- FOOTER -->
<footer class="container muted" style="text-align:center;">
  <small>Panel Admin • Trivias UTP</small>
</footer>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/datatables/datatables.min.js"></script>

<script>
$(function () {
  if ($.fn.DataTable) {
    $('.datatable').DataTable({
      dom: 'lfrtip',
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50],
      order: [],
      language: {
        search: "Buscar:",
        lengthMenu: "Mostrar _MENU_ registros",
        info: "Mostrando _START_ a _END_ de _TOTAL_",
        infoEmpty: "Mostrando 0 a 0 de 0",
        zeroRecords: "No hay registros",
        paginate: { previous: "Anterior", next: "Siguiente" }
      }
    });
  } else {
    console.error("DataTables no cargó");
  }
});
</script>


<script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/app.js"></script>

</body>
</html>
