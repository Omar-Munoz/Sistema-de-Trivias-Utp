<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Models\Topic;
use App\Models\Level;
use App\Models\Progress;
use App\Models\Question;
use App\Models\QuestionSet;
use App\Models\AnswerLog;
use App\Models\LevelRun;
use App\Core\Database;
use App\Utils\Sanitizer;

final class GameController extends Controller {

  public function selectTopic(): void {
    $this->requireLogin();
    $topics = (new Topic())->all();
    $levels = (new Level())->all();
    $this->view('game/select_topic', compact('topics','levels'));
  }

  public function start(): void {
    $this->requireLogin();
    $u = Session::get('user');

    $topicId = Sanitizer::int($_POST['topic_id'] ?? 0);
    $levelId = Sanitizer::int($_POST['level_id'] ?? 0);

    $levels = (new Level())->all();
    $level = null;
    foreach($levels as $l){ if ((int)$l['id'] === $levelId) $level = $l; }
    if (!$level) { Session::flash('err','Nivel inválido.'); Response::redirect('/play'); }

    $progress = new Progress();
    if (!$progress->canAccessLevel((int)$u['id'], $topicId, (int)$level['sort_order'])) {
      Session::flash('err','Primero completa el nivel anterior (100%).');
      Response::redirect('/play');
    }

    // V2: Batch de preguntas (ej: 10)
    $limit = 10;
    $questions = (new Question())->randomBatch($topicId, $levelId, $limit);
    if (!$questions) {
      Session::flash('err','No hay preguntas para ese tema/nivel.');
      Response::redirect('/play');
    }

    $qIds = array_map(fn($x)=>(int)$x['id'], $questions);

    // V2: Run (tiempo total del nivel)
    $runId = (new LevelRun())->start((int)$u['id'], $topicId, $levelId, null, count($qIds));

    Session::set('game', [
      'topic_id'=>$topicId,
      'level_id'=>$levelId,
      'question_set_id'=>null,
      'run_id'=>$runId,
      'q_ids'=>$qIds,
      'idx'=>0,
      'correct'=>0,
      'points'=>0,
      'started_at'=>microtime(true),
      'last_q_at'=>microtime(true)
    ]);

    Response::redirect('/play/session');
  }

  public function qrJoin(): void {
    $this->requireLogin();
    $u = Session::get('user');

    $code = Sanitizer::str($_GET['code'] ?? '');
    $set = (new QuestionSet())->findByCode($code);
    if (!$set) { http_response_code(404); echo "Set no existe"; return; }

    $limit = (int)$set['limit_questions'];
    $questions = (new Question())->randomBatch((int)$set['topic_id'], (int)$set['level_id'], $limit);
    if (!$questions) { http_response_code(400); echo "El set no tiene preguntas disponibles"; return; }

    $qIds = array_map(fn($x)=>(int)$x['id'], $questions);

    $runId = (new LevelRun())->start((int)$u['id'], (int)$set['topic_id'], (int)$set['level_id'], (int)$set['id'], count($qIds));

    Session::set('game', [
      'topic_id'=>(int)$set['topic_id'],
      'level_id'=>(int)$set['level_id'],
      'question_set_id'=>(int)$set['id'],
      'set_code'=>$set['code'],
      'run_id'=>$runId,
      'q_ids'=>$qIds,
      'idx'=>0,
      'correct'=>0,
      'points'=>0,
      'started_at'=>microtime(true),
      'last_q_at'=>microtime(true)
    ]);

    Response::redirect('/play/session');
  }

  public function session(): void {
    $this->requireLogin();
    $g = Session::get('game');
    if (!$g) Response::redirect('/play');

    $idx = (int)$g['idx'];
    $qIds = $g['q_ids'] ?? [];
    if ($idx >= count($qIds)) {
      Response::redirect('/play/finish');
    }

    $qid = (int)$qIds[$idx];
    $db = Database::pdo();
    $st = $db->prepare("SELECT * FROM questions WHERE id=? LIMIT 1");
    $st->execute([$qid]);
    $q = $st->fetch();
    if (!$q) { Response::redirect('/play/finish'); }

    Session::set('current_question_id', $qid);
    Session::set('last_q_at', microtime(true));

    $this->view('game/play_v2', ['q'=>$q, 'g'=>$g, 'idx'=>$idx, 'total'=>count($qIds)]);
  }

  public function answer(): void {
    $this->requireLogin();
    $u = Session::get('user');
    $g = Session::get('game');
    if (!$g) Response::redirect('/play');

    $qid = (int)Session::get('current_question_id', 0);
    $given = Sanitizer::str($_POST['answer'] ?? '');
    $given = strtoupper(trim($given));

// Normaliza True/False a A/B si tu BD guarda A=TRUE y B=FALSE
if ($given === 'T') $given = 'A';
if ($given === 'F') $given = 'B';


    $db = Database::pdo();
    $st = $db->prepare("SELECT correct_answer, points FROM questions WHERE id=? LIMIT 1");
    $st->execute([$qid]);
    $q = $st->fetch();
    if (!$q) Response::redirect('/play/session');

    $sec = microtime(true) - (float)Session::get('last_q_at', microtime(true));
    $correct = (strtoupper($given) === strtoupper($q['correct_answer'])) ? 1 : 0;

    (new AnswerLog())->log((int)$u['id'], $qid, $given, $correct, (float)$sec, $g['question_set_id'] ?? null);

    if ($correct) {
      $g['correct'] += 1;
      $g['points'] += (int)$q['points'];
      Session::flash('ok','¡Correcto!');
    } else {
      Session::flash('err','Incorrecto.');
    }

    $g['idx'] += 1;
    $g['last_q_at'] = microtime(true);
    Session::set('game', $g);

    Response::redirect('/play/session');
  }

  public function finish(): void {
  $this->requireLogin();
  $u = Session::get('user');
  $g = Session::get('game');
  if (!$g) { Response::redirect('/play'); return; }

  $totalQ  = count($g['q_ids'] ?? []);
  $correct = (int)($g['correct'] ?? 0);
  $points  = (int)($g['points'] ?? 0);

  $percent = $totalQ > 0 ? round(($correct / $totalQ) * 100, 2) : 0;

  // guarda progreso
  (new Progress())->upsert((int)$u['id'], (int)$g['topic_id'], (int)$g['level_id'], $points, $percent);

  // guarda run
  $totalSeconds = microtime(true) - (float)$g['started_at'];
  (new LevelRun())->finish((int)$g['run_id'], $correct, (float)$totalSeconds, $points);

  // promedio por pregunta (del juego actual)
  $avgSec = $totalQ > 0 ? round($totalSeconds / $totalQ, 2) : 0;

  // si venía de QR
  $setCode = $g['set_code'] ?? null;

  //  guardar resumen para pantalla de resultados
  Session::set('last_game_result', [
    'topic_id' => (int)$g['topic_id'],
    'level_id' => (int)$g['level_id'],
    'total' => $totalQ,
    'correct' => $correct,
    'wrong' => max(0, $totalQ - $correct),
    'percent' => $percent,
    'points' => $points,
    'total_seconds' => round($totalSeconds, 2),
    'avg_seconds' => $avgSec,
    'set_code' => $setCode, // null si no era QR
  ]);

  Session::forget('game');

  //  SIEMPRE muestra resultados primero
  Response::redirect('/play/result');
  return;
}


  public function leaderboard(): void {
    $this->requireLogin();
    $code = Sanitizer::str($_GET['code'] ?? '');
    $set = (new QuestionSet())->findByCode($code);
    if (!$set) { http_response_code(404); echo "Set no existe"; return; }

    $db = Database::pdo();
    $st = $db->prepare("
      SELECT 
        u.nickname,
        COUNT(al.id) answered,
        SUM(al.is_correct) correct,
        SUM(CASE WHEN al.is_correct=1 THEN q.points ELSE 0 END) points,
        AVG(al.seconds_to_answer) avg_sec
      FROM answer_logs al
      JOIN users u ON u.id=al.user_id
      JOIN questions q ON q.id=al.question_id
      WHERE al.question_set_id=?
      GROUP BY al.user_id
      ORDER BY points DESC, correct DESC, avg_sec ASC
    ");
    $st->execute([(int)$set['id']]);
    $rows = $st->fetchAll();

    $this->view('game/leaderboard', ['rows'=>$rows, 'code'=>$code]);
  }
  public function result(): void {
  $this->requireLogin();

  $result = Session::get('last_game_result');
  if (!$result) {
    Response::redirect('/progress');
    return;
  }

  $this->view('game/result', ['r' => $result]);
}

}
