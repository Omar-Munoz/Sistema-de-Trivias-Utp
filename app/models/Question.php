<?php
namespace App\Models;

final class Question extends BaseModel {

  public function list(int $topicId=0, int $levelId=0): array {
    $sql = "SELECT q.*, t.name topic, l.name level
            FROM questions q
            JOIN topics t ON t.id=q.topic_id
            JOIN levels l ON l.id=q.level_id
            WHERE 1=1";
    $params = [];
    if ($topicId>0) { $sql.=" AND q.topic_id=?"; $params[]=$topicId; }
    if ($levelId>0) { $sql.=" AND q.level_id=?"; $params[]=$levelId; }
    $sql.=" ORDER BY q.id DESC";
    $st = $this->db->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
  }

  public function create(array $data): int {
    $st = $this->db->prepare("INSERT INTO questions(topic_id,level_id,type,question_text,option_a,option_b,option_c,option_d,correct_answer,points) 
                              VALUES(?,?,?,?,?,?,?,?,?,?)");
    $st->execute([
      $data['topic_id'],$data['level_id'],$data['type'],$data['question_text'],
      $data['option_a'],$data['option_b'],$data['option_c'],$data['option_d'],
      $data['correct_answer'],$data['points']
    ]);
    return (int)$this->db->lastInsertId();
  }

  public function delete(int $id): void {
    $st = $this->db->prepare("DELETE FROM questions WHERE id=?");
    $st->execute([$id]);
  }

  // NUEVO V2: obtener N preguntas random del tema/nivel
  public function randomBatch(int $topicId, int $levelId, int $limit=10): array {
    $limit = max(1, min(50, $limit));
    $st = $this->db->prepare("SELECT * FROM questions WHERE topic_id=? AND level_id=? ORDER BY RAND() LIMIT {$limit}");
    $st->execute([$topicId, $levelId]);
    return $st->fetchAll();
  }
}
