<?php ob_start(); ?>

<div class="card section">
  <h2>Dashboard</h2>
  <p class="muted">Resumen general del sistema: módulos, propósito y cantidad de registros.</p>
</div>

<div class="grid2" style="margin-top:14px;">

  <div class="card section">
    <h3>Usuarios</h3>
    <p class="muted">
      Administración de usuarios (admin/operator/player), edición y control de avatar activo.
    </p>
    <p><strong>Total:</strong> <?= (int)($counts['users'] ?? 0) ?></p>
    <div class="row" style="margin-top:10px;">
      <a class="btn gray" href="<?= $baseUrl ?>/admin/users">Ir</a>
    </div>
  </div>

  <div class="card section">
    <h3>Temas</h3>
    <p class="muted">
      Gestión de temas disponibles en el sistema (PHP, JavaScript, Laravel, etc.).
    </p>
    <p><strong>Total:</strong> <?= (int)($counts['topics'] ?? 0) ?></p>
    <div class="row" style="margin-top:10px;">
      <a class="btn gray" href="<?= $baseUrl ?>/admin/topics">Ir</a>
    </div>
  </div>

  <div class="card section">
    <h3>Preguntas</h3>
    <p class="muted">
      Gestión del banco de preguntas por tema y nivel (opciones A–D / True-False).
    </p>
    <p><strong>Total:</strong> <?= (int)($counts['questions'] ?? 0) ?></p>
    <div class="row" style="margin-top:10px;">
      <a class="btn gray" href="<?= $baseUrl ?>/admin/questions">Ir</a>
    </div>
  </div>

  <div class="card section">
    <h3>Sets / QR</h3>
    <p class="muted">
      Conjuntos de preguntas para partidas multijugador y acceso mediante QR.
    </p>
    <p><strong>Total:</strong> <?= (int)($counts['sets'] ?? 0) ?></p>
    <div class="row" style="margin-top:10px;">
      <a class="btn gray" href="<?= $baseUrl ?>/admin/sets">Ir</a>
    </div>
  </div>

  <div class="card section">
    <h3>Premios</h3>
    <p class="muted">
      Premios por logro: incluye imagen, puntos, y relación por nivel/conocimiento.
    </p>
    <p><strong>Total:</strong> <?= (int)($counts['prizes'] ?? 0) ?></p>
    <div class="row" style="margin-top:10px;">
      <a class="btn gray" href="<?= $baseUrl ?>/admin/prizes">Ir</a>
    </div>
  </div>

  <div class="card section">
    <h3>Reportes (Excel)</h3>
    <p class="muted">
      Exportación para análisis: jugadores, avance, tiempos por pregunta y métricas generales.
    </p>
    <p><strong>Logs:</strong> <?= (int)($counts['answers'] ?? 0) ?> •
       <strong>Progreso:</strong> <?= (int)($counts['progress'] ?? 0) ?></p>
    <div class="row" style="margin-top:10px;">
      <a class="btn gray" href="<?= $baseUrl ?>/admin/reports/excel">Ir</a>
    </div>
  </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
