<?php ob_start(); ?>

<!-- HERO / INTRO -->
<div class="card section">
  <h2>Bienvenido a Trivias UTP</h2>
  <p class="muted">
    Aprende jugando: selecciona un tema, responde preguntas por nivel y gana puntos.
    También puedes entrar a partidas mediante código QR para competir con otros jugadores.
  </p>

  <div class="row" style="margin-top:12px;">
    <a class="btn" href="<?= $baseUrl ?>/register">Registrarme</a>
    <a class="btn gray" href="<?= $baseUrl ?>/login">Entrar</a>
  </div>
</div>

<!-- TEMAS -->
<div class="card section" style="margin-top:14px;">
  <h2>Temas disponibles</h2>
  <p class="muted">Cada tema incluye niveles progresivos y preguntas alineadas a tu conocimiento.</p>

  <div class="grid2" style="margin-top:12px;">
    <div class="card tight">
      <h3>🐘 PHP</h3>
      <p class="muted">
        Domina fundamentos del backend: variables, formularios, sesiones, PDO y buenas prácticas para aplicaciones web.
      </p>
      <div class="row" style="margin-top:10px;">
        <a class="btn gray" href="<?= $baseUrl ?>/play">Jugar PHP</a>
      </div>
    </div>

    <div class="card tight">
      <h3>⚡ JavaScript</h3>
      <p class="muted">
        Mejora tu lógica en el navegador: eventos, DOM, fetch, promesas y conceptos esenciales del frontend moderno.
      </p>
      <div class="row" style="margin-top:10px;">
        <a class="btn gray" href="<?= $baseUrl ?>/play">Jugar JS</a>
      </div>
    </div>

    <div class="card tight">
      <h3>🧩 Laravel</h3>
      <p class="muted">
        Aprende el framework más usado en PHP: rutas, controladores, Blade, Eloquent, migraciones y arquitectura limpia.
      </p>
      <div class="row" style="margin-top:10px;">
        <a class="btn gray" href="<?= $baseUrl ?>/play">Jugar Laravel</a>
      </div>
    </div>

    <div class="card tight">
      <h3>🏁 ¿Listo para competir?</h3>
      <p class="muted">
        Únete con QR a un set de preguntas y compara tu puntuación con otros jugadores.
        Ideal para dinámicas en clase.
      </p>
      <div class="row" style="margin-top:10px;">
        <a class="btn gray" href="<?= $baseUrl ?>/login">Entrar y jugar</a>
      </div>
    </div>
  </div>
</div>

<!-- NIVELES (CONTENEDOR LARGO) -->
<div class="card section" style="margin-top:14px;">
  <h2>Niveles de dificultad</h2>
  <p class="muted">
    El sistema está diseñado con 3 niveles para guiar tu aprendizaje y medir tu avance de manera progresiva:
  </p>

  <div class="grid2" style="margin-top:12px;">
    <div class="card tight">
      <h3>🟢 Principiante</h3>
      <p class="muted">
        Ideal para iniciar desde cero. Preguntas básicas para construir fundamentos sólidos.
      </p>
    </div>

    <div class="card tight">
      <h3>🟡 Novato</h3>
      <p class="muted">
        Requiere comprensión intermedia. Aquí empiezas a conectar conceptos y resolver situaciones reales.
      </p>
    </div>

    <div class="card tight">
      <h3>🔴 Experto</h3>
      <p class="muted">
        Desafío avanzado. Perfecto para demostrar dominio, velocidad y precisión.
      </p>
    </div>

    <div class="card tight">
      <h3>🔒 Progreso por niveles</h3>
      <p class="muted">
        No podrás pasar a niveles avanzados sin completar el nivel anterior, asegurando aprendizaje real y ordenado.
      </p>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
