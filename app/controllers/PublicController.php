<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Progress;
use App\Models\Prize;
use App\Models\LevelRun;

final class PublicController extends Controller {

  public function home(): void {
    $this->view('public/home');
  }

public function progress(): void
{
  $this->requireLogin();
  $u = \App\Core\Session::get('user');

  $db = \App\Core\Database::pdo();

  $st = $db->prepare("
    SELECT
      p.topic_id,
      p.level_id,
      p.total_points,
      p.percent_complete,
      p.first_completed_at,
      p.last_completed_at,
      t.name AS topic,
      l.name AS level
    FROM progress p
    JOIN topics t ON t.id = p.topic_id
    JOIN levels l ON l.id = p.level_id
    WHERE p.user_id = ?
    ORDER BY p.topic_id, p.level_id
  ");
  $st->execute([(int)$u['id']]);
  $progress = $st->fetchAll();

  $this->view('public/progress', [
    'u' => $u,
    'progress' => $progress
  ]);
}



  public function myPrizes(): void {
    $this->requireLogin();
    $u = Session::get('user');

    $earned = (new Prize())->earnedByUser((int)$u['id']);
    $this->view('public/my_prizes', ['earned'=>$earned, 'u'=>$u]);
  }
}
