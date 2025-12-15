<?php
namespace App\Services;

use App\Models\User;

final class AuthService {
  public function login(string $email, string $password): ?array {
    $model = new User();
    $u = $model->findByEmail($email);
    if (!$u) return null;
    if (!password_verify($password, $u['password_hash'])) return null;

    unset($u['password_hash']);
    return $u;
  }
}
