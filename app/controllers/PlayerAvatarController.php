<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;

final class PlayerAvatarController extends Controller
{
  public function upload(): void
  {
    $this->requireLogin();
    $u = Session::get('user');

    if (!isset($_FILES['avatar']) || ($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
      Session::flash('err', 'Debes seleccionar una imagen.');
      Response::redirect('/progress');
    }

    if (($_FILES['avatar']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
      Session::flash('err', 'Error al subir imagen.');
      Response::redirect('/progress');
    }

    $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['png','jpg','jpeg','webp'], true)) {
      Session::flash('err', 'Formato inválido. Usa png/jpg/webp.');
      Response::redirect('/progress');
    }

    $dir = __DIR__ . '/../../public/uploads/avatars';
    if (!is_dir($dir)) @mkdir($dir, 0777, true);

    $filename = 'u' . (int)$u['id'] . '_' . uniqid('', true) . '.' . $ext;
    $dest = $dir . '/' . $filename;

    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
      Session::flash('err', 'No se pudo guardar la imagen.');
      Response::redirect('/progress');
    }

    $db = Database::pdo();
    // desactiva anteriores
    $db->prepare("UPDATE avatars SET is_active=0 WHERE user_id=?")->execute([(int)$u['id']]);
    // inserta nuevo activo
    $db->prepare("INSERT INTO avatars(user_id, filename, is_active) VALUES(?,?,1)")
       ->execute([(int)$u['id'], $filename]);

    Session::flash('ok', 'Avatar actualizado.');
    Response::redirect('/progress');
  }
  public function clear(): void
{
  $this->requireLogin();
  $u = Session::get('user');

  $db = Database::pdo();
  // deja todos inactivos, no borra (por si quieres historial)
  $db->prepare("UPDATE avatars SET is_active=0 WHERE user_id=?")->execute([(int)$u['id']]);

  Session::flash('ok', 'Avatar quitado.');
  Response::redirect('/progress');
}

}
