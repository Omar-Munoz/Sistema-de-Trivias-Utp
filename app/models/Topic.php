<?php
namespace App\Models;

final class Topic extends BaseModel {
  public function all(): array {
    return $this->db->query("SELECT * FROM topics ORDER BY id DESC")->fetchAll();
  }

  public function create(string $name): int {
    $st = $this->db->prepare("INSERT INTO topics(name) VALUES(?)");
    $st->execute([$name]);
    return (int)$this->db->lastInsertId();
  }

  public function update(int $id, string $name): void {
    $st = $this->db->prepare("UPDATE topics SET name=? WHERE id=?");
    $st->execute([$name,$id]);
  }

  public function delete(int $id): void {
    $st = $this->db->prepare("DELETE FROM topics WHERE id=?");
    $st->execute([$id]);
  }
}
