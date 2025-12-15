<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class AdminDashboardController extends Controller
{
  public function index(): void
  {
    $this->requireStaff();

    $db = Database::pdo();

    $counts = [
      'users'     => (int)$db->query("SELECT COUNT(*) c FROM users")->fetch()['c'],
      'topics'    => (int)$db->query("SELECT COUNT(*) c FROM topics")->fetch()['c'],
      'questions' => (int)$db->query("SELECT COUNT(*) c FROM questions")->fetch()['c'],
      'sets'      => (int)$db->query("SELECT COUNT(*) c FROM question_sets")->fetch()['c'],
      'prizes'    => (int)$db->query("SELECT COUNT(*) c FROM prizes")->fetch()['c'],

      // extras útiles (si existen en tu BD)
      'progress'  => (int)$db->query("SELECT COUNT(*) c FROM progress")->fetch()['c'],
      'answers'   => (int)$db->query("SELECT COUNT(*) c FROM answer_logs")->fetch()['c'],
    ];

    $this->view('admin/dashboard', ['counts' => $counts]);
  }
}

