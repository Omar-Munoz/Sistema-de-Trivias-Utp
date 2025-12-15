<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Utils\Sanitizer;

final class AuthController extends Controller
{
public function registerForm(): void
{
  if (\App\Core\Session::get('user')) {
    \App\Core\Response::redirect('/progress');
  }
  $this->view('auth/register');
}

  public function register(): void
  {
    $email    = Sanitizer::email($_POST['email'] ?? '');
    $name     = Sanitizer::str($_POST['name'] ?? '');
    $nickname = Sanitizer::str($_POST['nickname'] ?? '');
    $pass     = (string)($_POST['password'] ?? '');

    if ($email === '' || $name === '' || $nickname === '' || $pass === '') {
      Session::flash('err', 'Todos los campos son obligatorios.');
      Response::redirect('/register');
    }

    if (strlen($pass) < 6) {
      Session::flash('err', 'La contraseña debe tener al menos 6 caracteres.');
      Response::redirect('/register');
    }

    $db = Database::pdo();

    $st = $db->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $st->execute([$email]);
    if ($st->fetch()) {
      Session::flash('err', 'Ese correo ya está registrado.');
      Response::redirect('/register');
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);

    // Registro público -> player
    $st = $db->prepare("INSERT INTO users(email, name, nickname, password_hash, role) VALUES(?,?,?,?,?)");
    $st->execute([$email, $name, $nickname, $hash, 'player']);

    Session::flash('ok', 'Registro exitoso. Ahora inicia sesión.');
    Response::redirect('/login');
  }

public function loginForm(): void
{
  if (\App\Core\Session::get('user')) {
    \App\Core\Response::redirect('/progress');
  }
  $this->view('auth/login');
}

  public function login(): void
  {
    $email = Sanitizer::email($_POST['email'] ?? '');
    $pass  = (string)($_POST['password'] ?? '');

    if ($email === '' || $pass === '') {
      Session::flash('err', 'Correo y contraseña son obligatorios.');
      Response::redirect('/login');
    }

    $db = Database::pdo();
    $st = $db->prepare("SELECT id, email, name, nickname, password_hash, role FROM users WHERE email=? LIMIT 1");
    $st->execute([$email]);
    $user = $st->fetch();

    if (!$user || !password_verify($pass, (string)$user['password_hash'])) {
      Session::flash('err', 'Credenciales incorrectas.');
      Response::redirect('/login');
    }

    // ✅ permitir operator/admin/player
    if (!in_array($user['role'], ['admin', 'operator', 'player'], true)) {
      Session::flash('err', 'Rol no permitido.');
      Response::redirect('/login');
    }

    unset($user['password_hash']);
    Session::set('user', $user);

    // ✅ admin y operator al mismo panel
    if ($user['role'] === 'admin' || $user['role'] === 'operator') {
      Response::redirect('/admin');
    }

    Response::redirect('/progress');
  }

  public function logout(): void
  {
    Session::forget('user');
    Session::flash('ok', 'Sesión cerrada.');
    Response::redirect('/login');
  }
}
