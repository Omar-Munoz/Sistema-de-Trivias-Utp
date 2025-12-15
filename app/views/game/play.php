<?php ob_start(); ?>
<div class="card">
  <h2>Pregunta</h2>
  <p><?= htmlspecialchars($q['question_text']) ?></p>

  <form method="POST" action="<?= $baseUrl ?>/play/answer">
    <?php if ($q['type'] === 'tf'): ?>
      <label><input type="radio" name="answer" value="T" required> True</label><br>
      <label><input type="radio" name="answer" value="F" required> False</label><br>
    <?php else: ?>
      <label><input type="radio" name="answer" value="A" required> A) <?= htmlspecialchars($q['option_a']) ?></label><br>
      <label><input type="radio" name="answer" value="B" required> B) <?= htmlspecialchars($q['option_b']) ?></label><br>
      <label><input type="radio" name="answer" value="C" required> C) <?= htmlspecialchars($q['option_c']) ?></label><br>
      <label><input type="radio" name="answer" value="D" required> D) <?= htmlspecialchars($q['option_d']) ?></label><br>
    <?php endif; ?>

    <p><strong>Puntos actuales:</strong> <?= (int)$g['points'] ?></p>
    <button class="btn" type="submit">Responder</button>
  </form>

  <a class="btn gray" href="<?= $baseUrl ?>/progress">Regresar</a>

</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/public.php'; ?>
