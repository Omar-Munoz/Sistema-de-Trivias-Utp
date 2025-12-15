<?php
namespace App\Models;

final class User extends BaseModel {
  public function create(string $email, string $name, string $nickname, string $password, string $role='player'): int {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $st = $this->db->prepare("INSERT INTO users(email,name,nickname,password_hash,role) VALUES(?,?,?,?,?)");
    $st->execute([$email,$name,$nickname,$hash,$role]);
    return (int)$this->db->lastInsertId();
  }

  public function findByEmail(string $email): ?array {
    $st = $this->db->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $st->execute([$email]);
    $u = $st->fetch();
    return $u ?: null;
  }

  public function allAdminsOps(): array {
    return $this->db->query("SELECT id,email,name,nickname,role,created_at FROM users WHERE role IN('admin','operator') ORDER BY id DESC")->fetchAll();
  }

  public function updateAdminOp(int $id, string $email, string $name, string $nickname, string $role): void {
    $st = $this->db->prepare("UPDATE users SET email=?, name=?, nickname=?, role=? WHERE id=?");
    $st->execute([$email,$name,$nickname,$role,$id]);
  }

  public function delete(int $id): void {
    $st = $this->db->prepare("DELETE FROM users WHERE id=?");
    $st->execute([$id]);
  }
}
