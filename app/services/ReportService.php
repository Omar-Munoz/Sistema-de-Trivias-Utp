<?php
namespace App\Services;

use App\Core\Database;

final class ReportService
{
  public function exportExcelProgress(): void
{
  if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
    throw new \RuntimeException("PhpSpreadsheet no está disponible. Revisa vendor/autoload.php.");
  }

  $db = \App\Core\Database::pdo();

  // 1) MISMO PROGRESO que la sección "Mi Progreso" (por user/topic/level)
  $progress = $db->query("
    SELECT
      u.id  AS user_id,
      u.email,
      u.nickname,
      u.role,
      t.name AS topic,
      l.name AS level,
      p.total_points,
      p.percent_complete,
      p.first_completed_at,
      p.last_completed_at,
      p.topic_id,
      p.level_id
    FROM progress p
    JOIN users u  ON u.id = p.user_id
    JOIN topics t ON t.id = p.topic_id
    JOIN levels l ON l.id = p.level_id
    ORDER BY u.id, p.topic_id, p.level_id
  ")->fetchAll();

  // 2) Métricas desde answer_logs:
  // - promedio "seconds_to_answer"
  // - promedio "segundos entre preguntas" usando created_at vs la anterior (por user/topic/level)
  //
  // NOTA: answer_logs no tiene topic_id/level_id; se obtiene con JOIN a questions.
  $metrics = [];

  // 2.1 Promedio de seconds_to_answer por user/topic/level
  try {
    $rows = $db->query("
      SELECT
        al.user_id,
        q.topic_id,
        q.level_id,
        AVG(al.seconds_to_answer) AS avg_seconds_to_answer
      FROM answer_logs al
      JOIN questions q ON q.id = al.question_id
      GROUP BY al.user_id, q.topic_id, q.level_id
    ")->fetchAll();

    foreach ($rows as $r) {
      $key = $r['user_id'].'|'.$r['topic_id'].'|'.$r['level_id'];
      $metrics[$key]['avg_seconds_to_answer'] = (float)$r['avg_seconds_to_answer'];
    }
  } catch (\Throwable $e) {
    // si falla, se deja vacío
  }

  // 2.2 Promedio del tiempo entre preguntas (created_at diff con la anterior)
  // Intento con LAG (MySQL 8+). Si falla, uso fallback con variables.
  try {
    $rows = $db->query("
      SELECT
        user_id, topic_id, level_id,
        AVG(TIMESTAMPDIFF(SECOND, prev_created_at, created_at)) AS avg_between_seconds
      FROM (
        SELECT
          al.user_id,
          q.topic_id,
          q.level_id,
          al.created_at,
          LAG(al.created_at) OVER (
            PARTITION BY al.user_id, q.topic_id, q.level_id
            ORDER BY al.created_at
          ) AS prev_created_at
        FROM answer_logs al
        JOIN questions q ON q.id = al.question_id
        WHERE al.created_at IS NOT NULL
      ) x
      WHERE prev_created_at IS NOT NULL
      GROUP BY user_id, topic_id, level_id
    ")->fetchAll();

    foreach ($rows as $r) {
      $key = $r['user_id'].'|'.$r['topic_id'].'|'.$r['level_id'];
      $metrics[$key]['avg_between_seconds'] = (int)round((float)$r['avg_between_seconds']);
    }
  } catch (\Throwable $e) {
    // Fallback sin LAG
    try {
      $db->query("SET @prev := NULL, @grp := ''");

      $rows = $db->query("
        SELECT user_id, topic_id, level_id, AVG(delta_seconds) AS avg_between_seconds
        FROM (
          SELECT
            al.user_id,
            q.topic_id,
            q.level_id,
            al.created_at,
            IF(@grp = CONCAT(al.user_id,'|',q.topic_id,'|',q.level_id) AND @prev IS NOT NULL,
                TIMESTAMPDIFF(SECOND, @prev, al.created_at),
                NULL
            ) AS delta_seconds,
            @prev := al.created_at,
            @grp := CONCAT(al.user_id,'|',q.topic_id,'|',q.level_id) AS grp_marker
          FROM answer_logs al
          JOIN questions q ON q.id = al.question_id
          WHERE al.created_at IS NOT NULL
          ORDER BY al.user_id, q.topic_id, q.level_id, al.created_at
        ) z
        WHERE delta_seconds IS NOT NULL
        GROUP BY user_id, topic_id, level_id
      ")->fetchAll();

      foreach ($rows as $r) {
        $key = $r['user_id'].'|'.$r['topic_id'].'|'.$r['level_id'];
        $metrics[$key]['avg_between_seconds'] = (int)round((float)$r['avg_between_seconds']);
      }
    } catch (\Throwable $e2) {
      // si falla, se deja vacío
    }
  }

  // 3) Crear Excel
  $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();
  $sheet->setTitle('Progreso');

  $headers = [
    'User ID','Email','Nickname','Role',
    'Topic','Level',
    'Total Points','% Complete',
    'First Completed','Last Completed',
    'Avg Seconds To Answer',
    'Avg Seconds Between Questions'
  ];
  $sheet->fromArray($headers, null, 'A1');

  $r = 2;
  foreach ($progress as $p) {
    $key = $p['user_id'].'|'.$p['topic_id'].'|'.$p['level_id'];

    $avgAnswer = $metrics[$key]['avg_seconds_to_answer'] ?? null;
    $avgBetween = $metrics[$key]['avg_between_seconds'] ?? null;

    // deja 2 decimales para avgAnswer
    if ($avgAnswer !== null) $avgAnswer = round((float)$avgAnswer, 2);

    $sheet->fromArray([
      (int)$p['user_id'],
      (string)$p['email'],
      (string)$p['nickname'],
      (string)$p['role'],
      (string)$p['topic'],
      (string)$p['level'],
      (int)$p['total_points'],
      (int)$p['percent_complete'],
      $p['first_completed_at'] ?? null,
      $p['last_completed_at'] ?? null,
      $avgAnswer,
      $avgBetween
    ], null, 'A'.$r);

    $r++;
  }

  foreach (range('A','L') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
  }

  $filename = 'reporte_progreso_' . date('Ymd_His') . '.xlsx';

  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment; filename="'.$filename.'"');
  header('Cache-Control: max-age=0');

  $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
  $writer->save('php://output');
  exit;
}
}
