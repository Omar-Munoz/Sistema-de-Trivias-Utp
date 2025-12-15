<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Utils\Sanitizer;

final class TopicsController extends Controller
{
  public function index(): void
  {
    $this->requireStaff();
    $rows = Database::pdo()
      ->query("SELECT * FROM topics ORDER BY id DESC")
      ->fetchAll();

    $this->view('admin/topics', ['rows'=>$rows]);
  }

  public function create(): void
  {
    $this->requireStaff();
    $name = Sanitizer::str($_POST['name'] ?? '');

    if ($name === '') {
      Session::flash('err', 'Nombre requerido.');
      Response::redirect('/admin/topics'); return;
    }

    $st = Database::pdo()->prepare("INSERT INTO topics(name) VALUES(?)");
    $st->execute([$name]);

    Session::flash('ok', 'Tema creado.');
    Response::redirect('/admin/topics');
  }

  public function update(): void
  {
    $this->requireStaff();
    $id   = Sanitizer::int($_POST['id'] ?? 0);
    $name = Sanitizer::str($_POST['name'] ?? '');

    if ($id<=0 || $name==='') {
      Session::flash('err', 'Datos inválidos.');
      Response::redirect('/admin/topics'); return;
    }

    Database::pdo()
      ->prepare("UPDATE topics SET name=? WHERE id=?")
      ->execute([$name,$id]);

    Session::flash('ok', 'Tema actualizado.');
    Response::redirect('/admin/topics');
  }

  public function delete(): void
  {
    $this->requireStaff();
    $id = Sanitizer::int($_POST['id'] ?? 0);

    if ($id<=0) {
      Session::flash('err', 'ID inválido.');
      Response::redirect('/admin/topics'); return;
    }

    Database::pdo()
      ->prepare("DELETE FROM topics WHERE id=?")
      ->execute([$id]);

    Session::flash('ok', 'Tema eliminado.');
    Response::redirect('/admin/topics');
  }
}
