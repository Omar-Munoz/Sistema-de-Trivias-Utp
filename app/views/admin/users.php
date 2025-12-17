<?php ob_start(); ?>

<h2>Usuarios</h2>

<div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
  <a class="btn gray" href="<?= $baseUrl ?>/admin">⬅ Regresar al Dashboard</a>
  <button class="btn" onclick="openModal('createUserModal')">➕ Nuevo Usuario</button>
</div>

<br>

<table class="table datatable">
  <thead>
    <tr>
      <th>ID</th>
      <th>Avatar</th>
      <th>Correo</th>
      <th>Nombre</th>
      <th>Apodo</th>
      <th>Rol</th>
      <th>Acciones</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($rows as $u): ?>
      <tr>
        <td><?= (int)$u['id'] ?></td>

        <td>
          <?php if (!empty($u['active_avatar'])): ?>
            <img src="<?= $baseUrl ?>/uploads/avatars/<?= htmlspecialchars($u['active_avatar']) ?>"
                 style="width:48px;height:48px;border-radius:12px;object-fit:cover;border:1px solid #ddd;">
          <?php else: ?>
            <img src="<?= $baseUrl ?>/assets/img/default-avatar.png"
                 style="width:48px;height:48px;border-radius:12px;object-fit:cover;border:1px solid #ddd;opacity:.85;">
          <?php endif; ?>
        </td>

        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['nickname']) ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td>

        <td style="white-space:nowrap;">
          <button class="btn" onclick='openEditUser(<?= json_encode($u, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'>
            Editar
          </button>

          <form method="POST" action="<?= $baseUrl ?>/admin/users/delete" style="display:inline;"
                onsubmit="return confirm('¿Eliminar usuario?');">
            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <button class="btn red" type="submit">Eliminar</button>
          </form>

          <form method="POST" action="<?= $baseUrl ?>/admin/users/avatar/toggle" style="display:inline;">
            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
            <input type="hidden" name="mode" value="<?= !empty($u['active_avatar']) ? 'off' : 'on_latest' ?>">
            <button class="btn gray" type="submit">
              <?= !empty($u['active_avatar']) ? 'Desactivar Avatar' : 'Activar Avatar' ?>
            </button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- MODAL CREAR -->
<div id="createUserModal" class="modal">
  <div class="card section">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
      <h3 style="margin:0;">Crear Usuario</h3>
      <button class="btn gray" type="button" onclick="closeModal('createUserModal')">X</button>
    </div>

    <form method="POST" action="<?= $baseUrl ?>/admin/users/create" style="margin-top:10px;">
      <label>Email</label>
      <input type="email" name="email" required>

      <label>Nombre</label>
      <input type="text" name="name" required>

      <label>Apodo</label>
      <input type="text" name="nickname" required>

      <label>Rol</label>
      <select name="role" required>
        <option value="player">player</option>
        <option value="operator">operator</option>
        <option value="admin">admin</option>
      </select>

      <label>Password</label>
      <input type="password" name="password" required>

      <br>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn" type="submit">Guardar</button>
        <button class="btn gray" type="button" onclick="closeModal('createUserModal')">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDITAR -->
<div id="editUserModal" class="modal">
  <div class="card section">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
      <h3 style="margin:0;">Editar Usuario</h3>
      <button class="btn gray" type="button" onclick="closeModal('editUserModal')">X</button>
    </div>

    <form method="POST" action="<?= $baseUrl ?>/admin/users/update" style="margin-top:10px;">
      <input type="hidden" name="id" id="eu_id">

      <label>Email</label>
      <input type="email" name="email" id="eu_email" required>

      <label>Nombre</label>
      <input type="text" name="name" id="eu_name" required>

      <label>Apodo</label>
      <input type="text" name="nickname" id="eu_nickname" required>

      <label>Rol</label>
      <select name="role" id="eu_role" required>
        <option value="player">player</option>
        <option value="operator">operator</option>
        <option value="admin">admin</option>
      </select>

      <br>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn" type="submit">Guardar Cambios</button>
        <button class="btn gray" type="button" onclick="closeModal('editUserModal')">Cancelar</button>
      </div>
    </form>
  </div>
</div>


<script>
function openModal(id){ document.getElementById(id).style.display='block'; }
function closeModal(id){ document.getElementById(id).style.display='none'; }

function openEditUser(u){
  document.getElementById('eu_id').value = u.id;
  document.getElementById('eu_email').value = u.email || '';
  document.getElementById('eu_name').value = u.name || '';
  document.getElementById('eu_nickname').value = u.nickname || '';
  document.getElementById('eu_role').value = u.role || 'player';
  openModal('editUserModal');
}

// cerrar al click afuera
window.addEventListener('click', (e)=>{
  ['createUserModal','editUserModal'].forEach(id=>{
    const m = document.getElementById(id);
    if (m && e.target === m) m.style.display='none';
  });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
