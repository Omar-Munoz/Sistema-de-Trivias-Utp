<?php
namespace App\Models;

use App\Utils\Time;

final class LevelRun extends BaseModel {

  public function start(int $userId, int $topicId, int $levelId, ?int $setId, int $totalQuestions): int {
    $st = $this->db->prepare("INSERT INTO level_runs(user_id,topic_id,level_id,question_set_id,started_at,total_questions)
                              VALUES(?,?,?,?,?,?)");
    $st->execute([$userId,$topicId,$levelId,$setId, Time::now(), $totalQuestions]);
    return (int)$this->db->lastInsertId();
  }

  public function finish(int $runId, int $correctCount, float $totalSeconds, int $pointsEarned): void {
    $st = $this->db->prepare("UPDATE level_runs 
                              SET finished_at=?, total_seconds=?, correct_count=?, points_earned=?
                              WHERE id=?");
    $st->execute([Time::now(), $totalSeconds, $correctCount, $pointsEarned, $runId]);
  }

  public function lastRunsByUser(int $userId, int $limit=20): array {
    $limit = max(1, min(100, $limit));
    $st = $this->db->prepare("SELECT lr.*, t.name topic, l.name level
                              FROM level_runs lr
                              JOIN topics t ON t.id=lr.topic_id
                              JOIN levels l ON l.id=lr.level_id
                              WHERE lr.user_id=?
                              ORDER BY lr.id DESC
                              LIMIT {$limit}");
    $st->execute([$userId]);
    return $st->fetchAll();
  }
}
