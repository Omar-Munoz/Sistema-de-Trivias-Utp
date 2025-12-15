<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Utils\Sanitizer;

final class QuestionsController extends Controller
{
  public function index(): void
  {
    $this->requireStaff();

    $db = Database::pdo();
    $rows = $db->query("
      SELECT q.*, t.name AS topic, l.name AS level
      FROM questions q
      JOIN topics t ON t.id=q.topic_id
      JOIN levels l ON l.id=q.level_id
      ORDER BY q.id DESC
    ")->fetchAll();

    $topics = $db->query("SELECT * FROM topics ORDER BY id")->fetchAll();
    $levels = $db->query("SELECT * FROM levels ORDER BY sort_order")->fetchAll();

    $this->view('admin/questions', ['rows'=>$rows,'topics'=>$topics,'levels'=>$levels]);
  }

  public function create(): void
  {
    $this->requireStaff();

    $topicId = Sanitizer::int($_POST['topic_id'] ?? 0);
    $levelId = Sanitizer::int($_POST['level_id'] ?? 0);
    $type    = Sanitizer::str($_POST['type'] ?? 'mcq');
    $text    = Sanitizer::str($_POST['question_text'] ?? '');
    $a       = Sanitizer::str($_POST['option_a'] ?? '');
    $b       = Sanitizer::str($_POST['option_b'] ?? '');
    $c       = Sanitizer::str($_POST['option_c'] ?? '');
    $d       = Sanitizer::str($_POST['option_d'] ?? '');
    $correct = strtoupper(Sanitizer::str($_POST['correct_answer'] ?? ''));
    $points  = Sanitizer::int($_POST['points'] ?? 10);

    if ($topicId<=0 || $levelId<=0 || $text==='') {
      Session::flash('err','Datos inválidos.');
      Response::redirect('/admin/questions');
    }

    if ($type === 'tf') {
      if (!in_array($correct, ['T','F'], true)) $correct = 'T';
      $a=$b=$c=$d=null;
    } else {
      $type = 'mcq';
      if (!in_array($correct, ['A','B','C','D'], true)) $correct = 'A';
    }

    $db = Database::pdo();
    $st = $db->prepare("INSERT INTO questions(topic_id,level_id,type,question_text,option_a,option_b,option_c,option_d,correct_answer,points)
                        VALUES(?,?,?,?,?,?,?,?,?,?)");
    $st->execute([$topicId,$levelId,$type,$text,$a,$b,$c,$d,$correct,$points]);

    Session::flash('ok','Pregunta creada.');
    Response::redirect('/admin/questions');
  }

  public function update(): void
  {
    $this->requireStaff();

    $id      = Sanitizer::int($_POST['id'] ?? 0);
    $topicId = Sanitizer::int($_POST['topic_id'] ?? 0);
    $levelId = Sanitizer::int($_POST['level_id'] ?? 0);
    $type    = Sanitizer::str($_POST['type'] ?? 'mcq');
    $text    = Sanitizer::str($_POST['question_text'] ?? '');
    $a       = Sanitizer::str($_POST['option_a'] ?? '');
    $b       = Sanitizer::str($_POST['option_b'] ?? '');
    $c       = Sanitizer::str($_POST['option_c'] ?? '');
    $d       = Sanitizer::str($_POST['option_d'] ?? '');
    $correct = strtoupper(Sanitizer::str($_POST['correct_answer'] ?? ''));
    $points  = Sanitizer::int($_POST['points'] ?? 10);

    if ($id<=0 || $topicId<=0 || $levelId<=0 || $text==='') {
      Session::flash('err','Datos inválidos para editar.');
      Response::redirect('/admin/questions');
    }

    if ($type === 'tf') {
      if (!in_array($correct, ['T','F'], true)) $correct = 'T';
      $a=$b=$c=$d=null;
    } else {
      $type = 'mcq';
      if (!in_array($correct, ['A','B','C','D'], true)) $correct = 'A';
    }

    $db = Database::pdo();
    $st = $db->prepare("UPDATE questions
                        SET topic_id=?, level_id=?, type=?, question_text=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_answer=?, points=?
                        WHERE id=?");
    $st->execute([$topicId,$levelId,$type,$text,$a,$b,$c,$d,$correct,$points,$id]);

    Session::flash('ok','Pregunta actualizada.');
    Response::redirect('/admin/questions');
  }

  public function delete(): void
  {
    $this->requireStaff();

    $id = Sanitizer::int($_POST['id'] ?? 0);
    if ($id<=0) {
      Session::flash('err','ID inválido.');
      Response::redirect('/admin/questions');
    }

    $db = Database::pdo();
    $db->prepare("DELETE FROM questions WHERE id=?")->execute([$id]);

    Session::flash('ok','Pregunta eliminada.');
    Response::redirect('/admin/questions');
  }
}
