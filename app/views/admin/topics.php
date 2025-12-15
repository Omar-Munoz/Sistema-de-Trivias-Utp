<?php ob_start(); ?>

<div class="card section">
  <div class="row" style="justify-content:space-between;">
    <h2>Gestión de Temas</h2>
    <button class="btn" onclick="openCreate()">➕ Nuevo Tema</button>
  </div>

  <table class="datatable" style="margin-top:14px;">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre del Tema</th>
        <th style="width:180px;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td>
            <button
              class="btn gray"
              onclick="openEdit(<?= (int)$r['id'] ?>,'<?= htmlspecialchars($r['name'], ENT_QUOTES) ?>')">
              ✏ Editar
            </button>

            <form method="POST"
                  action="<?= $baseUrl ?>/admin/topics/delete"
                  style="display:inline"
                  onsubmit="return confirm('¿Eliminar este tema?');">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn red">🗑 Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- ================= MODAL CREAR ================= -->
<div class="modal" id="modalCreate">
  <div class="modal-content">
    <h3>Crear Tema</h3>

    <form method="POST" action="<?= $baseUrl ?>/admin/topics/create">
      <label>Nombre del Tema</label>
      <input name="name" required>

      <div class="row" style="margin-top:12px;">
        <button class="btn" type="submit">Guardar</button>
        <button type="button" class="btn gray" onclick="closeCreate()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= MODAL EDITAR ================= -->
<div class="modal" id="modalEdit">
  <div class="modal-content">
    <h3>Editar Tema</h3>

    <form method="POST" action="<?= $baseUrl ?>/admin/topics/update">
      <input type="hidden" name="id" id="edit_id">

      <label>Nombre del Tema</label>
      <input name="name" id="edit_name" required>

      <div class="row" style="margin-top:12px;">
        <button class="btn" type="submit">Actualizar</button>
        <button type="button" class="btn gray" onclick="closeEdit()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= JS ================= -->
<script>
function openCreate() {
  document.getElementById('modalCreate').style.display = 'block';
}
function closeCreate() {
  document.getElementById('modalCreate').style.display = 'none';
}

function openEdit(id, name) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_name').value = name;
  document.getElementById('modalEdit').style.display = 'block';
}
function closeEdit() {
  document.getElementById('modalEdit').style.display = 'none';
}

// cerrar modal al hacer click fuera
window.addEventListener('click', function(e){
  document.querySelectorAll('.modal').forEach(m=>{
    if (e.target === m) m.style.display = 'none';
  });
});
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
