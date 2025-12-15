<?php
namespace App\Models;

final class Avatar extends BaseModel {
  public function byUser(int $userId): array {
    $st = $this->db->prepare("SELECT * FROM avatars WHERE user_id=? ORDER BY id DESC");
    $st->execute([$userId]);
    return $st->fetchAll();
  }

  public function add(int $userId, string $filename): int {
    $st = $this->db->prepare("INSERT INTO avatars(user_id,filename,is_active) VALUES(?,?,0)");
    $st->execute([$userId,$filename]);
    return (int)$this->db->lastInsertId();
  }

  public function toggle(int $id, int $userId, int $active): void {
    // desactiva todos y activa uno (si active=1)
    if ($active === 1) {
      $st = $this->db->prepare("UPDATE avatars SET is_active=0 WHERE user_id=?");
      $st->execute([$userId]);
    }
    $st = $this->db->prepare("UPDATE avatars SET is_active=? WHERE id=? AND user_id=?");
    $st->execute([$active,$id,$userId]);
  }
}
