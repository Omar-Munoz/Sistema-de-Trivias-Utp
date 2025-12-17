<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class RankingsController extends Controller
{
  public function index(): void
  {
    $this->requireLogin(); // solo logeados

    $db = Database::pdo();

    // Ranking por puntos totales (suma de progress.total_points)
    // Premios: top 3 imágenes según progreso completado + points_required
    $st = $db->query("
      SELECT
        u.id,
        u.nickname,
        COALESCE(a.filename, '') AS active_avatar,
        COALESCE(SUM(p.total_points), 0) AS points,
        (
          SELECT GROUP_CONCAT(t.image SEPARATOR '|')
          FROM (
            SELECT pr.image
            FROM prizes pr
            JOIN progress pp
              ON pp.user_id = u.id
             AND pp.topic_id = pr.topic_id
             AND pp.level_id = pr.level_id
            WHERE pp.percent_complete >= 100
              AND pp.total_points >= pr.points_required
            ORDER BY pr.points_required DESC, pr.id DESC
            LIMIT 3
          ) AS t
        ) AS prize_images
      FROM users u
      LEFT JOIN avatars a
        ON a.user_id = u.id AND a.is_active = 1
      LEFT JOIN progress p
        ON p.user_id = u.id
      WHERE u.role = 'player'
      GROUP BY u.id, u.nickname, a.filename
      ORDER BY points DESC, u.id ASC
      LIMIT 50
    ");

    $rows = $st->fetchAll();
    $this->view('public/ranking', ['rows' => $rows]);
  }
}
