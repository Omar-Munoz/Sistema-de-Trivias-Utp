<?php ob_start(); ?>
<div class="card">
  <h2>Mis Premios</h2>
  <a class="btn gray" href="<?= $baseUrl ?>/progress">Volver</a>

  <hr>

  <?php if (empty($earned)): ?>
    <p>Aún no has ganado premios. Completa niveles (100%) y acumula puntos.</p>
  <?php else: ?>
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:12px;">
      <?php foreach($earned as $p): ?>
        <div class="card">
          <img src="<?= $baseUrl ?>/uploads/prizes/<?= htmlspecialchars($p['image']) ?>" style="width:100%; border-radius:10px;">
          <h3><?= htmlspecialchars($p['title']) ?></h3>
          <p><strong>Tema:</strong> <?= htmlspecialchars($p['topic']) ?></p>
          <p><strong>Nivel:</strong> <?= htmlspecialchars($p['level']) ?></p>
          <p><strong>Puntos req:</strong> <?= (int)$p['points_required'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/public.php'; ?>
