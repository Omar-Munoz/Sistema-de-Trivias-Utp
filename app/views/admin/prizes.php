<?php ob_start(); ?>
<h2>Premios</h2>

<div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
  <a class="btn gray" href="<?= $baseUrl ?>/admin">⬅ Regresar al Dashboard</a>
  <button class="btn" onclick="openModal('createPrizeModal')">➕ Nuevo Premio</button>
</div>

<br>

<table class="table datatable">
  <thead>
    <tr>
      <th>ID</th>
      <th>Tema</th>
      <th>Nivel</th>
      <th>Título</th>
      <th>Puntos Req.</th>
      <th>Imagen</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $p): ?>
      <tr>
        <td><?= (int)$p['id'] ?></td>
        <td><?= htmlspecialchars($p['topic']) ?></td>
        <td><?= htmlspecialchars($p['level']) ?></td>
        <td><?= htmlspecialchars($p['title']) ?></td>
        <td><?= (int)$p['points_required'] ?></td>
        <td>
          <?php if (!empty($p['image'])): ?>
            <img src="<?= $baseUrl ?>/uploads/prizes/<?= htmlspecialchars($p['image']) ?>"
            style="width:48px;height:48px;border-radius:12px;object-fit:cover;border:1px solid #ddd;">
          <?php else: ?>
            —
          <?php endif; ?>
        </td>
        <td style="white-space:nowrap;">
          <button class="btn" onclick='openEditPrize(<?= json_encode($p, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'>
            Editar
          </button>

          <form method="POST" action="<?= $baseUrl ?>/admin/prizes/delete" style="display:inline;"
                onsubmit="return confirm('¿Eliminar este premio?');">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <button class="btn red" type="submit">Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- MODAL: CREAR PREMIO -->
<div id="createPrizeModal" class="modal">
  <div class="card section">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
      <h3 style="margin:0;">Crear Premio</h3>
      <button class="btn gray" type="button" onclick="closeModal('createPrizeModal')">X</button>
    </div>

    <form method="POST" action="<?= $baseUrl ?>/admin/prizes/create" enctype="multipart/form-data" style="margin-top:10px;">
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

      <label>Título</label>
      <input type="text" name="title" required>

      <label>Puntos requeridos</label>
      <input type="number" name="points_required" value="20" min="0" required>

      <label>Imagen (png/jpg/webp)</label>
      <input type="file" name="image" accept="image/*" required>

      <br>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn" type="submit">Guardar</button>
        <button class="btn gray" type="button" onclick="closeModal('createPrizeModal')">Cancelar</button>
        <a class="btn gray" href="<?= $baseUrl ?>/admin">Regresar</a>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: EDITAR PREMIO -->
<div id="editPrizeModal" class="modal">
  <div class="card section">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
      <h3 style="margin:0;">Editar Premio</h3>
      <button class="btn gray" type="button" onclick="closeModal('editPrizeModal')">X</button>
    </div>

    

    <div id="ep_preview" style="margin-top:10px; display:flex; gap:10px; align-items:center;">
      <img id="ep_img" src="" alt="Preview" style="width:70px;height:70px;object-fit:cover;border-radius:12px;display:none;">
      <div style="font-size:12px; opacity:.8;">
        <div><strong>Imagen actual:</strong> <span id="ep_img_name">—</span></div>
        <div>(Si subes una nueva, reemplaza la anterior)</div>
      </div>
    </div>

    <form method="POST" action="<?= $baseUrl ?>/admin/prizes/update" enctype="multipart/form-data" style="margin-top:10px;">
      <input type="hidden" name="id" id="ep_id">

      <label>Tema</label>
      <select name="topic_id" id="ep_topic_id" required>
        <?php foreach ($topics as $t): ?>
          <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Nivel</label>
      <select name="level_id" id="ep_level_id" required>
        <?php foreach ($levels as $l): ?>
          <option value="<?= (int)$l['id'] ?>"><?= htmlspecialchars($l['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Título</label>
      <input type="text" name="title" id="ep_title" required>

      <label>Puntos requeridos</label>
      <input type="number" name="points_required" id="ep_points_required" min="0" required>

      <label>Nueva imagen (opcional)</label>
      <input type="file" name="image" accept="image/*">

      <br>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn" type="submit">Guardar Cambios</button>
        <button class="btn gray" type="button" onclick="closeModal('editPrizeModal')">Cancelar</button>
        <a class="btn gray" href="<?= $baseUrl ?>/admin/prizes">Regresar</a>
      </div>
    </form>
  </div>
</div>

<!-- MODAL CSS + JS -->
<style>
.modal{display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; padding:20px;}
.modal-content{background:#fff; max-width:720px; margin:40px auto; padding:18px; border-radius:14px; box-shadow:0 10px 40px rgba(0,0,0,.25);}
.modal-content input, .modal-content select, .modal-content textarea{width:100%; margin-top:6px; margin-bottom:10px;}
.btn.red{background:#c0392b;}
</style>

<script>
function openModal(id){ document.getElementById(id).style.display='block'; }
function closeModal(id){ document.getElementById(id).style.display='none'; }

function openEditPrize(p){
  document.getElementById('ep_id').value = p.id;
  document.getElementById('ep_topic_id').value = p.topic_id;
  document.getElementById('ep_level_id').value = p.level_id;
  document.getElementById('ep_title').value = p.title || '';
  document.getElementById('ep_points_required').value = p.points_required ?? 0;

  const img = document.getElementById('ep_img');
  const nameSpan = document.getElementById('ep_img_name');

  if (p.image) {
    img.src = "<?= $baseUrl ?>/uploads/prizes/" + p.image;
    img.style.display = 'block';
    nameSpan.textContent = p.image;
  } else {
    img.style.display = 'none';
    nameSpan.textContent = '—';
  }

  openModal('editPrizeModal');
}

// Cierra modal con click fuera del contenido
window.addEventListener('click', (e)=>{
  ['createPrizeModal','editPrizeModal'].forEach(id=>{
    const m = document.getElementById(id);
    if (m && e.target === m) m.style.display='none';
  });
});
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
