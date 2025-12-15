<?php
namespace App\Models;

final class Prize extends BaseModel {

  public function all(): array {
    return $this->db->query("SELECT p.*, l.name level, t.name topic
                             FROM prizes p
                             JOIN levels l ON l.id=p.level_id
                             JOIN topics t ON t.id=p.topic_id
                             ORDER BY p.id DESC")->fetchAll();
  }

  public function create(int $topicId, int $levelId, string $title, string $image, int $points): int {
    $st = $this->db->prepare("INSERT INTO prizes(topic_id,level_id,title,image,points_required) VALUES(?,?,?,?,?)");
    $st->execute([$topicId,$levelId,$title,$image,$points]);
    return (int)$this->db->lastInsertId();
  }

  public function delete(int $id): void {
    $st = $this->db->prepare("DELETE FROM prizes WHERE id=?");
    $st->execute([$id]);
  }

  // NUEVO V2: premios que el jugador ya “puede reclamar/ver”
  public function earnedByUser(int $userId): array {
    $st = $this->db->prepare("
      SELECT p.*, t.name topic, l.name level
      FROM prizes p
      JOIN topics t ON t.id=p.topic_id
      JOIN levels l ON l.id=p.level_id
      JOIN progress pr ON pr.user_id=? AND pr.topic_id=p.topic_id AND pr.level_id=p.level_id
      WHERE pr.percent_complete >= 100 AND pr.total_points >= p.points_required
      ORDER BY t.id, l.sort_order
    ");
    $st->execute([$userId]);
    return $st->fetchAll();
  }
}
