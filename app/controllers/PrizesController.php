<?php
namespace App\Controllers;
use Endroid\QrCode\QrCode;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Utils\Sanitizer;

final class PrizesController extends Controller
{
  public function index(): void
  {
    $this->requireStaff();

    $db = Database::pdo();
    $rows = $db->query("
      SELECT p.*, t.name AS topic, l.name AS level
      FROM prizes p
      JOIN topics t ON t.id=p.topic_id
      JOIN levels l ON l.id=p.level_id
      ORDER BY p.id DESC
    ")->fetchAll();

    $topics = $db->query("SELECT * FROM topics ORDER BY id")->fetchAll();
    $levels = $db->query("SELECT * FROM levels ORDER BY sort_order")->fetchAll();

    $this->view('admin/prizes', ['rows'=>$rows,'topics'=>$topics,'levels'=>$levels]);
  }

  public function create(): void
  {
    $this->requireStaff();

    $topicId = Sanitizer::int($_POST['topic_id'] ?? 0);
    $levelId = Sanitizer::int($_POST['level_id'] ?? 0);
    $title   = Sanitizer::str($_POST['title'] ?? '');
    $points  = Sanitizer::int($_POST['points_required'] ?? 0);

    if ($topicId<=0 || $levelId<=0 || $title==='' || $points<0) {
      Session::flash('err','Datos inválidos.');
      Response::redirect('/admin/prizes');
    }

    $imageName = $this->handleUpload('image', __DIR__ . '/../../public/uploads/prizes');
    if ($imageName === null) {
      Session::flash('err','Debes subir una imagen.');
      Response::redirect('/admin/prizes');
    }

    $db = Database::pdo();
    $st = $db->prepare("INSERT INTO prizes(topic_id,level_id,title,image,points_required) VALUES(?,?,?,?,?)");
    $st->execute([$topicId,$levelId,$title,$imageName,$points]);

    Session::flash('ok','Premio creado.');
    Response::redirect('/admin/prizes');
  }

  public function update(): void
  {
    $this->requireStaff();

    $id      = Sanitizer::int($_POST['id'] ?? 0);
    $topicId = Sanitizer::int($_POST['topic_id'] ?? 0);
    $levelId = Sanitizer::int($_POST['level_id'] ?? 0);
    $title   = Sanitizer::str($_POST['title'] ?? '');
    $points  = Sanitizer::int($_POST['points_required'] ?? 0);

    if ($id<=0 || $topicId<=0 || $levelId<=0 || $title==='' || $points<0) {
      Session::flash('err','Datos inválidos para editar.');
      Response::redirect('/admin/prizes');
    }

    $db = Database::pdo();

    $newImage = $this->handleUpload('image', __DIR__ . '/../../public/uploads/prizes', true);
    if ($newImage !== null) {
      // borra imagen anterior
      $st = $db->prepare("SELECT image FROM prizes WHERE id=? LIMIT 1");
      $st->execute([$id]);
      $old = $st->fetch();
      if ($old && !empty($old['image'])) {
        $p = __DIR__ . '/../../public/uploads/prizes/' . $old['image'];
        if (is_file($p)) @unlink($p);
      }

      $st = $db->prepare("UPDATE prizes SET topic_id=?, level_id=?, title=?, image=?, points_required=? WHERE id=?");
      $st->execute([$topicId,$levelId,$title,$newImage,$points,$id]);
    } else {
      $st = $db->prepare("UPDATE prizes SET topic_id=?, level_id=?, title=?, points_required=? WHERE id=?");
      $st->execute([$topicId,$levelId,$title,$points,$id]);
    }

    Session::flash('ok','Premio actualizado.');
    Response::redirect('/admin/prizes');
  }

  public function delete(): void
  {
    $this->requireStaff();

    $id = Sanitizer::int($_POST['id'] ?? 0);
    if ($id<=0) {
      Session::flash('err','ID inválido.');
      Response::redirect('/admin/prizes');
    }

    $db = Database::pdo();
    $st = $db->prepare("SELECT image FROM prizes WHERE id=? LIMIT 1");
    $st->execute([$id]);
    $row = $st->fetch();

    if ($row && !empty($row['image'])) {
      $p = __DIR__ . '/../../public/uploads/prizes/' . $row['image'];
      if (is_file($p)) @unlink($p);
    }

    $db->prepare("DELETE FROM prizes WHERE id=?")->execute([$id]);

    Session::flash('ok','Premio eliminado.');
    Response::redirect('/admin/prizes');
  }

  private function handleUpload(string $field, string $dir, bool $optional=false): ?string
  {
    if (!isset($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
      return $optional ? null : null;
    }

    if (!is_dir($dir)) @mkdir($dir, 0777, true);

    if (($_FILES[$field]['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
      return null;
    }

    $tmp = $_FILES[$field]['tmp_name'];
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['png','jpg','jpeg','webp'], true)) return null;

    $name = uniqid('img_', true) . '.' . $ext;
    $dest = rtrim($dir,'/') . '/' . $name;

    return move_uploaded_file($tmp, $dest) ? $name : null;
  }
}
