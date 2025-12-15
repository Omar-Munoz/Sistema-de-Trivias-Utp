<?php ob_start(); ?>
<div class="card">
  <h2>Pregunta <?= ($idx+1) ?> / <?= $total ?></h2>

  <div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">
    <div><strong>Puntos:</strong> <?= (int)($g['points'] ?? 0) ?></div>
    <div><strong>Correctas:</strong> <?= (int)($g['correct'] ?? 0) ?></div>
    <div><strong>Tiempo (esta pregunta):</strong> <span id="t">0.0</span>s</div>
  </div>

  <hr>

  <p style="font-size:18px;"><?= htmlspecialchars($q['question_text']) ?></p>

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

    <br>
    <button class="btn" type="submit">Responder</button>
  </form>

</div>

<script>
let start = performance.now();
setInterval(()=>{
  let s = (performance.now() - start)/1000;
  document.getElementById('t').innerText = s.toFixed(1);
}, 100);
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/public.php'; ?>
