<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Utils\Sanitizer;

final class UsersController extends Controller
{
  public function index(): void
  {
    $this->requireStaff();

    $db = Database::pdo();
    $rows = $db->query("
      SELECT u.*,
        a.filename AS active_avatar
      FROM users u
      LEFT JOIN avatars a
        ON a.user_id = u.id AND a.is_active = 1
      ORDER BY u.id DESC
    ")->fetchAll();

    $this->view('admin/users', ['rows' => $rows]);
  }

  public function create(): void
  {
    $this->requireStaff();

    $sessionUser = Session::get('user');
    $sessionRole = (string)($sessionUser['role'] ?? '');

    $email    = Sanitizer::email($_POST['email'] ?? '');
    $name     = Sanitizer::str($_POST['name'] ?? '');
    $nickname = Sanitizer::str($_POST['nickname'] ?? '');
    $role     = Sanitizer::str($_POST['role'] ?? 'player');
    $pass     = (string)($_POST['password'] ?? '');

    if ($email === '' || $name === '' || $nickname === '' || $pass === '') {
      Session::flash('err', 'Completa todos los campos.');
      Response::redirect('/admin/users');
    }

    if (!in_array($role, ['admin','operator','player'], true)) {
      $role = 'player';
    }

    //  Restricción: operator NO puede crear admin
    if ($sessionRole === 'operator' && $role === 'admin') {
      Session::flash('err', 'Operator no puede crear usuarios admin.');
      Response::redirect('/admin/users');
    }

    $db = Database::pdo();

    // email único
    $st = $db->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $st->execute([$email]);
    if ($st->fetch()) {
      Session::flash('err', 'Ese correo ya existe.');
      Response::redirect('/admin/users');
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);

    $st = $db->prepare("INSERT INTO users(email,name,nickname,password_hash,role) VALUES(?,?,?,?,?)");
    $st->execute([$email, $name, $nickname, $hash, $role]);

    Session::flash('ok', 'Usuario creado.');
    Response::redirect('/admin/users');
  }

  public function update(): void
  {
    $this->requireStaff();

    $sessionUser = Session::get('user');
    $sessionRole = (string)($sessionUser['role'] ?? '');

    $id       = Sanitizer::int($_POST['id'] ?? 0);
    $email    = Sanitizer::email($_POST['email'] ?? '');
    $name     = Sanitizer::str($_POST['name'] ?? '');
    $nickname = Sanitizer::str($_POST['nickname'] ?? '');
    $role     = Sanitizer::str($_POST['role'] ?? 'player');

    if ($id <= 0 || $email === '' || $name === '' || $nickname === '') {
      Session::flash('err', 'Datos inválidos para actualizar.');
      Response::redirect('/admin/users');
    }

    if (!in_array($role, ['admin','operator','player'], true)) {
      $role = 'player';
    }

    $db = Database::pdo();

    // rol actual del usuario objetivo
    $st = $db->prepare("SELECT role FROM users WHERE id=? LIMIT 1");
    $st->execute([$id]);
    $target = $st->fetch();

    if (!$target) {
      Session::flash('err', 'Usuario no encontrado.');
      Response::redirect('/admin/users');
    }

    //  Restricciones operator
    if ($sessionRole === 'operator') {
      // no modificar admins existentes
      if (($target['role'] ?? '') === 'admin') {
        Session::flash('err', 'Operator no puede modificar usuarios admin.');
        Response::redirect('/admin/users');
      }
      // no asignar admin
      if ($role === 'admin') {
        Session::flash('err', 'Operator no puede asignar role admin.');
        Response::redirect('/admin/users');
      }
    }

    $st = $db->prepare("UPDATE users SET email=?, name=?, nickname=?, role=? WHERE id=?");
    $st->execute([$email, $name, $nickname, $role, $id]);

    Session::flash('ok', 'Usuario actualizado.');
    Response::redirect('/admin/users');
  }

  public function delete(): void
  {
    $this->requireStaff();

    $sessionUser = Session::get('user');
    $sessionRole = (string)($sessionUser['role'] ?? '');

    $id = Sanitizer::int($_POST['id'] ?? 0);
    if ($id <= 0) {
      Session::flash('err', 'ID inválido.');
      Response::redirect('/admin/users');
    }

    $db = Database::pdo();

    // rol del usuario objetivo
    $st = $db->prepare("SELECT role FROM users WHERE id=? LIMIT 1");
    $st->execute([$id]);
    $target = $st->fetch();

    if (!$target) {
      Session::flash('err', 'Usuario no encontrado.');
      Response::redirect('/admin/users');
    }

    // ✅ Restricción operator: no eliminar admin
    if ($sessionRole === 'operator' && ($target['role'] ?? '') === 'admin') {
      Session::flash('err', 'Operator no puede eliminar usuarios admin.');
      Response::redirect('/admin/users');
    }

    $st = $db->prepare("DELETE FROM users WHERE id=?");
    $st->execute([$id]);

    Session::flash('ok', 'Usuario eliminado.');
    Response::redirect('/admin/users');
  }

  // Activar / desactivar avatar del usuario (desde módulo Usuarios)
  public function toggleAvatar(): void
  {
    $this->requireStaff();

    $userId = Sanitizer::int($_POST['user_id'] ?? 0);
    $mode   = Sanitizer::str($_POST['mode'] ?? 'off'); // off | on_latest

    if ($userId <= 0) {
      Session::flash('err', 'Usuario inválido.');
      Response::redirect('/admin/users');
    }

    $db = Database::pdo();

    if ($mode === 'off') {
      $st = $db->prepare("UPDATE avatars SET is_active=0 WHERE user_id=?");
      $st->execute([$userId]);
      Session::flash('ok', 'Avatar desactivado.');
      Response::redirect('/admin/users');
    }

    // activar el último avatar subido
    $db->prepare("UPDATE avatars SET is_active=0 WHERE user_id=?")->execute([$userId]);

    $st = $db->prepare("SELECT id FROM avatars WHERE user_id=? ORDER BY id DESC LIMIT 1");
    $st->execute([$userId]);
    $row = $st->fetch();

    if (!$row) {
      Session::flash('err', 'Ese usuario no tiene avatares.');
      Response::redirect('/admin/users');
    }

    $db->prepare("UPDATE avatars SET is_active=1 WHERE id=?")->execute([(int)$row['id']]);
    Session::flash('ok', 'Avatar activado (último subido).');
    Response::redirect('/admin/users');
  }
}
