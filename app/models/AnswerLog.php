<?php
namespace App\Models;

final class AnswerLog extends BaseModel {
  public function log(int $userId, int $questionId, string $given, int $correct, float $seconds, int $setId=null): void {
    $st = $this->db->prepare("INSERT INTO answer_logs(user_id,question_id,answer_given,is_correct,seconds_to_answer,question_set_id)
                              VALUES(?,?,?,?,?,?)");
    $st->execute([$userId,$questionId,$given,$correct,$seconds,$setId]);
  }
}
