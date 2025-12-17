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

  <?php
// Detectar si es True/False (por texto en opciones)
$optA = trim((string)($q['option_a'] ?? ''));
$optB = trim((string)($q['option_b'] ?? ''));
$optC = trim((string)($q['option_c'] ?? ''));
$optD = trim((string)($q['option_d'] ?? ''));

$isTF =
  in_array(strtolower($optA), ['true','verdadero','v'], true) &&
  in_array(strtolower($optB), ['false','falso','f'], true) &&
  ($optC === '' && $optD === '');
?>

<form method="POST" action="<?= $baseUrl ?>/play/answer">
  <?php if ($isTF): ?>
    <!-- TRUE/FALSE -->
    <div class="row" style="gap:12px;">
      <button class="btn" type="submit" name="answer" value="T">True</button>
      <button class="btn gray" type="submit" name="answer" value="F">False</button>
    </div>

  <?php else: ?>
    <!-- A / B / C / D -->
    <div class="grid2" style="gap:12px;">
      <button class="btn gray" type="submit" name="answer" value="A">A) <?= htmlspecialchars($optA) ?></button>
      <button class="btn gray" type="submit" name="answer" value="B">B) <?= htmlspecialchars($optB) ?></button>
      <button class="btn gray" type="submit" name="answer" value="C">C) <?= htmlspecialchars($optC) ?></button>
      <button class="btn gray" type="submit" name="answer" value="D">D) <?= htmlspecialchars($optD) ?></button>
    </div>
  <?php endif; ?>
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
