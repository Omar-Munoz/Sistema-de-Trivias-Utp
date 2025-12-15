<?php ob_start(); ?>

<div class="card section">
  <h2>Resultados de la partida</h2>
  <p class="muted">Resumen del juego que acabas de finalizar.</p>

  <div class="grid2" style="margin-top:12px;">
    <div class="card tight">
      <h3>Desempeño</h3>
      <p>
        Total: <strong><?= (int)$r['total'] ?></strong><br>
        Correctas: <strong><?= (int)$r['correct'] ?></strong><br>
        Incorrectas: <strong><?= (int)$r['wrong'] ?></strong><br>
        Avance: <strong><?= htmlspecialchars((string)$r['percent']) ?>%</strong>
      </p>
    </div>

    <div class="card tight">
      <h3>Tiempo y puntos</h3>
      <p>
        Puntos: <strong><?= (int)$r['points'] ?></strong><br>
        Tiempo total: <strong><?= number_format((float)$r['total_seconds'], 2) ?>s</strong><br>
        Promedio/pregunta: <strong><?= number_format((float)$r['avg_seconds'], 2) ?>s</strong>
      </p>
    </div>
  </div>

  <div class="row" style="margin-top:14px;">
    <a class="btn" href="<?= $baseUrl ?>/play">Volver a jugar</a>
    <a class="btn gray" href="<?= $baseUrl ?>/progress">Salir</a>

    <?php if (!empty($r['set_code'])): ?>
      <a class="btn gray" href="<?= $baseUrl ?>/qr/leaderboard?code=<?= urlencode($r['set_code']) ?>">
        Ver ranking
      </a>
    <?php endif; ?>
  </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
