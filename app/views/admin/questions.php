<?php ob_start(); ?>
<h2>Preguntas</h2>

<div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
  <a class="btn gray" href="<?= $baseUrl ?>/admin">⬅ Regresar al Dashboard</a>
  <button class="btn" onclick="openModal('createQuestionModal')">➕ Nueva Pregunta</button>
</div>

<br>

<table class="datatable">
  <thead>
    <tr>
      <th>ID</th>
      <th>Tema</th>
      <th>Nivel</th>
      <th>Tipo</th>
      <th>Pregunta</th>
      <th>Correcta</th>
      <th>Puntos</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $q): ?>
      <tr>
        <td><?= (int)$q['id'] ?></td>
        <td><?= htmlspecialchars($q['topic']) ?></td>
        <td><?= htmlspecialchars($q['level']) ?></td>
        <td><?= htmlspecialchars($q['type']) ?></td>
        <td style="max-width:420px;"><?= htmlspecialchars($q['question_text']) ?></td>
        <td><?= htmlspecialchars($q['correct_answer']) ?></td>
        <td><?= (int)$q['points'] ?></td>
        <td style="white-space:nowrap;">
          <button class="btn" onclick='openEditQuestion(<?= json_encode($q, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'>
            Editar
          </button>

          <form method="POST" action="<?= $baseUrl ?>/admin/questions/delete" style="display:inline;"
                onsubmit="return confirm('¿Eliminar esta pregunta?');">
            <input type="hidden" name="id" value="<?= (int)$q['id'] ?>">
            <button class="btn red" type="submit">Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- MODAL: CREAR -->
<div id="createQuestionModal" class="modal">
  <div class="card section">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
      <h3 style="margin:0;">Crear Pregunta</h3>
      <button class="btn gray" type="button" onclick="closeModal('createQuestionModal')">X</button>
    </div>

    <form method="POST" action="<?= $baseUrl ?>/admin/questions/create" style="margin-top:10px;">
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

      <label>Tipo</label>
      <select name="type" id="cq_type" onchange="toggleQuestionType('cq_')" required>
        <option value="mcq">mcq (opciones A-D)</option>
        <option value="tf">tf (True/False)</option>
      </select>

      <label>Texto de la Pregunta</label>
      <textarea name="question_text" rows="3" required></textarea>

      <div id="cq_mcq_block">
        <label>Opción A</label>
        <input type="text" name="option_a">

        <label>Opción B</label>
        <input type="text" name="option_b">

        <label>Opción C</label>
        <input type="text" name="option_c">

        <label>Opción D</label>
        <input type="text" name="option_d">

        <label>Respuesta Correcta</label>
        <select name="correct_answer">
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>

      <div id="cq_tf_block" style="display:none;">
        <label>Respuesta Correcta</label>
        <select name="correct_answer">
          <option value="T">True</option>
          <option value="F">False</option>
        </select>
        <p style="font-size:12px; opacity:.75; margin:8px 0 0;">
          En modo True/False no se guardan opciones A-D.
        </p>
      </div>

      <label>Puntos</label>
      <input type="number" name="points" value="10" min="0" required>

      <br>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn" type="submit">Guardar</button>
        <button class="btn gray" type="button" onclick="closeModal('createQuestionModal')">Cancelar</button>
        <a class="btn gray" href="<?= $baseUrl ?>/admin">Regresar</a>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: EDITAR -->
<div id="editQuestionModal" class="modal">
  <div class="card section">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
      <h3 style="margin:0;">Editar Pregunta</h3>
      <button class="btn gray" type="button" onclick="closeModal('editQuestionModal')">X</button>
    </div>

    <form method="POST" action="<?= $baseUrl ?>/admin/questions/update" style="margin-top:10px;">
      <input type="hidden" name="id" id="eq_id">

      <label>Tema</label>
      <select name="topic_id" id="eq_topic_id" required>
        <?php foreach ($topics as $t): ?>
          <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Nivel</label>
      <select name="level_id" id="eq_level_id" required>
        <?php foreach ($levels as $l): ?>
          <option value="<?= (int)$l['id'] ?>"><?= htmlspecialchars($l['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Tipo</label>
      <select name="type" id="eq_type" onchange="toggleQuestionType('eq_')" required>
        <option value="mcq">mcq (opciones A-D)</option>
        <option value="tf">tf (True/False)</option>
      </select>

      <label>Texto de la Pregunta</label>
      <textarea name="question_text" id="eq_question_text" rows="3" required></textarea>

      <div id="eq_mcq_block">
        <label>Opción A</label>
        <input type="text" name="option_a" id="eq_option_a">

        <label>Opción B</label>
        <input type="text" name="option_b" id="eq_option_b">

        <label>Opción C</label>
        <input type="text" name="option_c" id="eq_option_c">

        <label>Opción D</label>
        <input type="text" name="option_d" id="eq_option_d">

        <label>Respuesta Correcta</label>
        <select name="correct_answer" id="eq_correct_mcq">
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>

      <div id="eq_tf_block" style="display:none;">
        <label>Respuesta Correcta</label>
        <select name="correct_answer" id="eq_correct_tf">
          <option value="T">True</option>
          <option value="F">False</option>
        </select>
      </div>

      <label>Puntos</label>
      <input type="number" name="points" id="eq_points" min="0" required>

      <br>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn" type="submit">Guardar Cambios</button>
        <button class="btn gray" type="button" onclick="closeModal('editQuestionModal')">Cancelar</button>
        <a class="btn gray" href="<?= $baseUrl ?>/admin/questions">Regresar</a>
      </div>
    </form>
  </div>
</div>

<!-- MODAL CSS + JS -->
<style>
.modal{display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; padding:20px;}
.modal-content{background:#fff; max-width:720px; margin:40px auto; padding:18px; border-radius:14px; box-shadow:0 10px 40px rgba(0,0,0,.25);}
.modal-content input, .modal-content select, .modal-content textarea{width:100%; margin-top:6px; margin-bottom:10px;}
.table, table{width:100%;}
.btn.red{background:#c0392b;}
</style>

<script>
function openModal(id){ document.getElementById(id).style.display='block'; }
function closeModal(id){ document.getElementById(id).style.display='none'; }

// Alterna UI según tipo (mcq/tf)
function toggleQuestionType(prefix){
  const type = document.getElementById(prefix + 'type').value;
  const mcq = document.getElementById(prefix + 'mcq_block');
  const tf  = document.getElementById(prefix + 'tf_block');
  if(type === 'tf'){
    mcq.style.display = 'none';
    tf.style.display  = 'block';
  } else {
    mcq.style.display = 'block';
    tf.style.display  = 'none';
  }
}

// Carga datos en modal editar
function openEditQuestion(q){
  document.getElementById('eq_id').value = q.id;
  document.getElementById('eq_topic_id').value = q.topic_id;
  document.getElementById('eq_level_id').value = q.level_id;
  document.getElementById('eq_type').value = q.type;
  document.getElementById('eq_question_text').value = q.question_text || '';

  document.getElementById('eq_option_a').value = q.option_a || '';
  document.getElementById('eq_option_b').value = q.option_b || '';
  document.getElementById('eq_option_c').value = q.option_c || '';
  document.getElementById('eq_option_d').value = q.option_d || '';

  document.getElementById('eq_points').value = q.points ?? 10;

  // según tipo, setear correcta en el select correcto
  if ((q.type || '').toLowerCase() === 'tf') {
    document.getElementById('eq_correct_tf').value = (q.correct_answer || 'T').toUpperCase();
  } else {
    document.getElementById('eq_correct_mcq').value = (q.correct_answer || 'A').toUpperCase();
  }

  toggleQuestionType('eq_');
  openModal('editQuestionModal');
}

// Cierra modal con click fuera del contenido
window.addEventListener('click', (e)=>{
  ['createQuestionModal','editQuestionModal'].forEach(id=>{
    const m = document.getElementById(id);
    if (m && e.target === m) m.style.display='none';
  });
});
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
