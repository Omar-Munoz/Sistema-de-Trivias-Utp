<?php ob_start(); ?>
<div class="card">
  <h2>Leaderboard (Set <?= htmlspecialchars($code) ?>)</h2>

  <?php if (!$rows): ?>
    <p>Aún no hay jugadores en este set.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Jugador</th>
          <th>Respondidas</th>
          <th>Correctas</th>
          <th>Puntos</th>
          <th>Prom. seg</th>
        </tr>
      </thead>
      <tbody>
        <?php $i=1; foreach($rows as $r): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($r['nickname']) ?></td>
            <td><?= (int)$r['answered'] ?></td>
            <td><?= (int)$r['correct'] ?></td>
            <td><?= (int)$r['points'] ?></td>
            <td><?= round((float)$r['avg_sec'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <br>
  <a class="btn gray" href="<?= $baseUrl ?>/play">Jugar otro</a>
  <a class="btn" href="<?= $baseUrl ?>/progress">Ver mi progreso</a>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/public.php'; ?>
