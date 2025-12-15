<?php
namespace App\Models;

final class Level extends BaseModel {
  public function all(): array {
    return $this->db->query("SELECT * FROM levels ORDER BY sort_order ASC")->fetchAll();
  }
}
