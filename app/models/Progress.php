<?php
namespace App\Models;

final class Progress extends BaseModel {

  public function get(int $userId): array {
    $st = $this->db->prepare("SELECT p.*, t.name topic, l.name level, l.sort_order
                              FROM progress p
                              JOIN topics t ON t.id=p.topic_id
                              JOIN levels l ON l.id=p.level_id
                              WHERE p.user_id=?
                              ORDER BY t.id, l.sort_order");
    $st->execute([$userId]);
    return $st->fetchAll();
  }

  public function upsert(int $userId, int $topicId, int $levelId, int $points, float $percent): void {
    // si llega a 100% guardamos timestamps de completado
    $completed = ($percent >= 100.0);

    $st = $this->db->prepare("
      INSERT INTO progress(user_id,topic_id,level_id,total_points,percent_complete,last_level_at,first_completed_at,last_completed_at)
      VALUES(?,?,?,?,?,NOW(),
        CASE WHEN ? THEN NOW() ELSE NULL END,
        CASE WHEN ? THEN NOW() ELSE NULL END
      )
      ON DUPLICATE KEY UPDATE 
        total_points = total_points + VALUES(total_points),
        percent_complete = GREATEST(percent_complete, VALUES(percent_complete)),
        last_level_at = NOW(),
        first_completed_at = COALESCE(first_completed_at, CASE WHEN ? THEN NOW() ELSE NULL END),
        last_completed_at = CASE WHEN ? THEN NOW() ELSE last_completed_at END
    ");
    $st->execute([$userId,$topicId,$levelId,$points,$percent,$completed,$completed,$completed,$completed]);
  }

  public function canAccessLevel(int $userId, int $topicId, int $levelSortOrder): bool {
    if ($levelSortOrder <= 1) return true;

    // debe tener completado (100%) el nivel anterior
    $st = $this->db->prepare("
      SELECT MAX(l.sort_order) as max_completed
      FROM progress p 
      JOIN levels l ON l.id=p.level_id
      WHERE p.user_id=? AND p.topic_id=? AND p.percent_complete >= 100
    ");
    $st->execute([$userId,$topicId]);
    $row = $st->fetch();
    $maxCompleted = (int)($row['max_completed'] ?? 0);

    return $maxCompleted >= ($levelSortOrder - 1);
  }
}
