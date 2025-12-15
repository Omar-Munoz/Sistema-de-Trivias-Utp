<?php
namespace App\Models;

final class QuestionSet extends BaseModel {
  public function create(int $topicId, int $levelId, int $createdBy, int $limitQuestions=10): string {
    $code = strtoupper(bin2hex(random_bytes(3))); // ej: A1B2C3
    $st = $this->db->prepare("INSERT INTO question_sets(code,topic_id,level_id,created_by,limit_questions) VALUES(?,?,?,?,?)");
    $st->execute([$code,$topicId,$levelId,$createdBy,$limitQuestions]);
    return $code;
  }

  public function findByCode(string $code): ?array {
    $st = $this->db->prepare("SELECT * FROM question_sets WHERE code=? LIMIT 1");
    $st->execute([$code]);
    $row = $st->fetch();
    return $row ?: null;
  }

  public function all(): array {
    return $this->db->query("SELECT qs.*, t.name topic, l.name level
                             FROM question_sets qs
                             JOIN topics t ON t.id=qs.topic_id
                             JOIN levels l ON l.id=qs.level_id
                             ORDER BY qs.id DESC")->fetchAll();
  }
}
