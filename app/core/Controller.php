<?php
namespace App\Core;

class Controller
{
  protected function view(string $view, array $data = []): void
  {
    $config = require __DIR__ . '/../config/config.php';
    $baseUrl = rtrim($config['app']['base_url'] ?? '', '');

    extract($data, EXTR_SKIP);

    $path = __DIR__ . '/../views/' . $view . '.php';
    if (!file_exists($path)) {
      throw new \RuntimeException("Vista no encontrada: $view");
    }

    require $path;
  }

  protected function requireLogin(): void
  {
    $u = Session::get('user');
    if (!$u) {
      Session::flash('err', 'Debes iniciar sesión.');
      Response::redirect('/login');
    }
  }

  // ✅ Admin y Operator (mismo acceso)
  protected function requireStaff(): void
  {
    $u = Session::get('user');
    if (!$u || !in_array($u['role'] ?? '', ['admin', 'operator'], true)) {
      Session::flash('err', 'Acceso restringido.');
      Response::redirect('/login');
    }
  }

  // (Opcional) Solo admin (no lo usaremos si operator = admin)
  protected function requireAdmin(): void
  {
    $u = Session::get('user');
    if (!$u || ($u['role'] ?? '') !== 'admin') {
      Session::flash('err', 'Solo administradores.');
      Response::redirect('/login');
    }
  }
}
