<?php ob_start(); ?>
<div class="card">
  <h2>Jugar</h2>
  <form method="POST" action="<?= $baseUrl ?>/play/start">
    <div class="row">
      <div>
        <label>Tema</label>
        <select name="topic_id" required>
          <?php foreach($topics as $t): ?>
            <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Nivel</label>
        <select name="level_id" required>
          <?php foreach($levels as $l): ?>
            <option value="<?= (int)$l['id'] ?>"><?= htmlspecialchars($l['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <button class="btn" type="submit">Iniciar</button>
  </form>
  
  <a class="btn gray" href="<?= $baseUrl ?>/progress">Regresar</a>

</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/public.php'; ?>
