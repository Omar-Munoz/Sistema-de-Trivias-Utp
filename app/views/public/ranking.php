<?php ob_start(); ?>

<div class="card section">
  <div class="topbar" style="align-items:flex-end;">
    <div>
      <h2>🏆 Ranking de Jugadores</h2>
      <p class="muted">Top 50 por puntos acumulados. Se muestran hasta 3 premios logrados.</p>
    </div>
    <a class="btn gray" href="<?= $baseUrl ?>/progress">⬅ Volver a Progreso</a>
  </div>
</div>

<div class="card section" style="margin-top:12px;">
  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Avatar</th>
        <th>Nick</th>
        <th>Puntos</th>
        <th>Premios (hasta 3)</th>
      </tr>
    </thead>
    <tbody>
      <?php $i=0; foreach(($rows ?? []) as $r): $i++; ?>
        <?php
          $avatar = !empty($r['active_avatar'])
            ? $baseUrl . '/uploads/avatars/' . htmlspecialchars($r['active_avatar'])
            : $baseUrl . '/assets/img/default-avatar.png';

          $prizeImgs = [];
          if (!empty($r['prize_images'])) {
            $prizeImgs = array_values(array_filter(explode('|', (string)$r['prize_images'])));
          }
        ?>
        <tr>
          <td><strong><?= $i ?></strong></td>

          <td style="width:70px;">
            <img src="<?= $avatar ?>"
                 alt="avatar"
                 style="width:46px;height:46px;border-radius:14px;object-fit:cover;border:1px solid rgba(255,255,255,.18);">
          </td>

          <td><?= htmlspecialchars($r['nickname'] ?? '—') ?></td>

          <td><strong><?= (int)($r['points'] ?? 0) ?></strong></td>

          <td>
            <?php if (!$prizeImgs): ?>
              <span class="muted">Sin premios aún</span>
            <?php else: ?>
              <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <?php foreach($prizeImgs as $img): ?>
                  <img
                    src="<?= $baseUrl ?>/uploads/prizes/<?= htmlspecialchars($img) ?>"
                    alt="premio"
                    style="width:44px;height:44px;border-radius:12px;object-fit:cover;border:1px solid rgba(255,255,255,.18);">
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($rows)): ?>
        <tr>
          <td colspan="5" class="muted">No hay jugadores para mostrar.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
