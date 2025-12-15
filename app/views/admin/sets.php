<?php ob_start(); ?>

<h2>Sets (QR)</h2>

<div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
  <a class="btn gray" href="<?= $baseUrl ?>/admin">⬅ Regresar al Dashboard</a>
  <button class="btn" onclick="openModal('createSetModal')">➕ Nuevo Set</button>
</div>

<br>

<table class="datatable">
  <thead>
    <tr>
      <th>ID</th>
      <th>Código</th>
      <th>Tema</th>
      <th>Nivel</th>
      <th>Límite</th>
      <th>QR</th>
      <th>Acciones</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($sets as $s): ?>
      <tr>
        <td><?= (int)$s['id'] ?></td>
        <td><strong><?= htmlspecialchars($s['code']) ?></strong></td>
        <td><?= htmlspecialchars($s['topic'] ?? '') ?></td>
        <td><?= htmlspecialchars($s['level'] ?? '') ?></td>
        <td><?= (int)($s['limit_questions'] ?? 0) ?></td>

        <td>
          <?php if (!empty($s['qr_image'])): ?>
            <img
              src="<?= $baseUrl ?>/uploads/qrcodes/<?= htmlspecialchars($s['qr_image']) ?>"
              style="width:70px;height:70px;object-fit:cover;border-radius:12px;border:1px solid #ddd;"
              alt="QR"
            >
            <div style="font-size:12px; opacity:.75; margin-top:4px;">
              <?= htmlspecialchars($s['qr_image']) ?>
            </div>
          <?php else: ?>
            <span style="opacity:.7;">(sin QR)</span>
          <?php endif; ?>
        </td>

        <td style="white-space:nowrap;">
          <button class="btn" onclick='openEditSet(<?= json_encode($s, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'>
            Editar
          </button>

          <form method="POST" action="<?= $baseUrl ?>/admin/sets/delete" style="display:inline;"
                onsubmit="return confirm('¿Eliminar este set?');">
            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
            <button class="btn red" type="submit">Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- MODAL CREAR -->
<div id="createSetModal" class="modal">
  <div class="card section">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
      <h3 style="margin:0;">Crear Set</h3>
      <button class="btn gray" type="button" onclick="closeModal('createSetModal')">X</button>
    </div>

    <form method="POST" action="<?= $baseUrl ?>/admin/sets/create" style="margin-top:10px;">
      <label>Tema</label>
      <select name="topic_id" required>
        <?php foreach ($topics as $t): ?>
          <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Nivel</label>
      <select name="level_id" required>
        <?php foreach ($levels as $l): ?>
          <option value="<?= (int)$l['id'] ?>"><?= htmlspecialchars($l['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Límite de preguntas</label>
      <input type="number" name="limit_questions" value="5" min="1" required>

      <br>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn" type="submit">Guardar</button>
        <button class="btn gray" type="button" onclick="closeModal('createSetModal')">Cancelar</button>
        <a class="btn gray" href="<?= $baseUrl ?>/admin/sets">Regresar</a>
      </div>

      <p style="font-size:12px; opacity:.75; margin-top:10px;">
        Al guardar, el sistema genera el código y el QR automáticamente.
      </p>
    </form>
  </div>
</div>

<!-- MODAL EDITAR -->
<div id="editSetModal" class="modal">
  <div class="card section">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
      <h3 style="margin:0;">Editar Set</h3>
      <button class="btn gray" type="button" onclick="closeModal('editSetModal')">X</button>
    </div>

    <form method="POST" action="<?= $baseUrl ?>/admin/sets/update" style="margin-top:10px;">
      <input type="hidden" name="id" id="es_id">

      <label>Tema</label>
      <select name="topic_id" id="es_topic_id" required>
        <?php foreach ($topics as $t): ?>
          <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Nivel</label>
      <select name="level_id" id="es_level_id" required>
        <?php foreach ($levels as $l): ?>
          <option value="<?= (int)$l['id'] ?>"><?= htmlspecialchars($l['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Límite de preguntas</label>
      <input type="number" name="limit_questions" id="es_limit" min="1" required>

      <div style="margin-top:10px; font-size:12px; opacity:.8;">
        <strong>Código:</strong> <span id="es_code"></span><br>
        <strong>QR:</strong> <span id="es_qr"></span>
      </div>

      <br>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn" type="submit">Guardar Cambios</button>
        <button class="btn gray" type="button" onclick="closeModal('editSetModal')">Cancelar</button>
        <a class="btn gray" href="<?= $baseUrl ?>/admin/sets">Regresar</a>
      </div>
    </form>
  </div>
</div>

<style>
.modal{display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; padding:20px;}
.modal-content{background:#fff; max-width:760px; margin:40px auto; padding:18px; border-radius:14px; box-shadow:0 10px 40px rgba(0,0,0,.25);}
.modal-content input, .modal-content select{width:100%; margin-top:6px; margin-bottom:10px;}
.btn.red{background:#c0392b; color:#fff;}
</style>

<script>
function openModal(id){ document.getElementById(id).style.display='block'; }
function closeModal(id){ document.getElementById(id).style.display='none'; }

function openEditSet(s){
  document.getElementById('es_id').value = s.id;
  document.getElementById('es_topic_id').value = s.topic_id;
  document.getElementById('es_level_id').value = s.level_id;
  document.getElementById('es_limit').value = s.limit_questions ?? 5;

  document.getElementById('es_code').textContent = s.code || '';
  document.getElementById('es_qr').textContent   = s.qr_image || '(sin QR)';

  openModal('editSetModal');
}

window.addEventListener('click', (e)=>{
  ['createSetModal','editSetModal'].forEach(id=>{
    const m = document.getElementById(id);
    if (m && e.target === m) m.style.display='none';
  });
});
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
