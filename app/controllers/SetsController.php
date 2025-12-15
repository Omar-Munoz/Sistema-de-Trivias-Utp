<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Utils\Sanitizer;

// Endroid QR Code (v5)
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

final class SetsController extends Controller
{
  // ===== LISTAR =====
  public function index(): void
  {
    $this->requireStaff();

    $db = Database::pdo();

    // Ajusta el nombre de la tabla si en tu BD es otro.
    // Aquí asumimos: question_sets
    $sets = $db->query("
      SELECT
        s.*,
        t.name AS topic,
        l.name AS level
      FROM question_sets s
      JOIN topics t ON t.id = s.topic_id
      JOIN levels l ON l.id = s.level_id
      ORDER BY s.id DESC
    ")->fetchAll();

    $topics = $db->query("SELECT id, name FROM topics ORDER BY id")->fetchAll();
    $levels = $db->query("SELECT id, name FROM levels ORDER BY id")->fetchAll();

    $this->view('admin/sets', [
      'sets'   => $sets,
      'topics' => $topics,
      'levels' => $levels,
    ]);
  }

  // ===== CREAR =====
  public function create(): void
  {
    $this->requireStaff();

    $topicId = Sanitizer::int($_POST['topic_id'] ?? 0);
    $levelId = Sanitizer::int($_POST['level_id'] ?? 0);
    $limit   = Sanitizer::int($_POST['limit_questions'] ?? 5);
    if ($limit <= 0) $limit = 5;

    if ($topicId <= 0 || $levelId <= 0) {
      Session::flash('err', 'Tema o nivel inválido.');
      Response::redirect('/admin/sets');
    }

    $db = Database::pdo();

    // Generar código único
    $code = $this->generateUniqueCode($db);

    // Insertar set (sin qr_image aún)
    // Ajusta columnas si tu tabla difiere.
    $createdBy = (int)(Session::get('user')['id'] ?? 0);

    $st = $db->prepare("
      INSERT INTO question_sets (code, topic_id, level_id, limit_questions, created_by, qr_image)
      VALUES (?, ?, ?, ?, ?, NULL)
    ");
    $st->execute([$code, $topicId, $levelId, $limit, $createdBy]);

    $setId = (int)$db->lastInsertId();

    // Generar QR
    try {
      $qrFilename = $this->generateQrForSet($code);
      $db->prepare("UPDATE question_sets SET qr_image=? WHERE id=?")
         ->execute([$qrFilename, $setId]);

      Session::flash('ok', "Set creado. QR generado: {$code}");
    } catch (\Throwable $e) {
      // El set ya se creó, solo falló el QR
      Session::flash('err', "Set creado ({$code}) pero NO se pudo generar el QR: " . $e->getMessage());
    }

    Response::redirect('/admin/sets');
  }

  // ===== ACTUALIZAR =====
  public function update(): void
  {
    $this->requireStaff();

    $id      = Sanitizer::int($_POST['id'] ?? 0);
    $topicId = Sanitizer::int($_POST['topic_id'] ?? 0);
    $levelId = Sanitizer::int($_POST['level_id'] ?? 0);
    $limit   = Sanitizer::int($_POST['limit_questions'] ?? 5);
    if ($limit <= 0) $limit = 5;

    if ($id <= 0 || $topicId <= 0 || $levelId <= 0) {
      Session::flash('err', 'Datos inválidos para actualizar.');
      Response::redirect('/admin/sets');
    }

    $db = Database::pdo();

    $st = $db->prepare("UPDATE question_sets SET topic_id=?, level_id=?, limit_questions=? WHERE id=?");
    $st->execute([$topicId, $levelId, $limit, $id]);

    Session::flash('ok', 'Set actualizado.');
    Response::redirect('/admin/sets');
  }

  // ===== ELIMINAR =====
  public function delete(): void
  {
    $this->requireStaff();

    $id = Sanitizer::int($_POST['id'] ?? 0);
    if ($id <= 0) {
      Session::flash('err', 'ID inválido.');
      Response::redirect('/admin/sets');
    }

    $db = Database::pdo();

    // Buscar qr_image para borrar archivo
    $st = $db->prepare("SELECT qr_image FROM question_sets WHERE id=? LIMIT 1");
    $st->execute([$id]);
    $row = $st->fetch();

    if ($row && !empty($row['qr_image'])) {
      $path = __DIR__ . '/../../public/uploads/qrcodes/' . $row['qr_image'];
      if (is_file($path)) @unlink($path);
    }

    $db->prepare("DELETE FROM question_sets WHERE id=?")->execute([$id]);

    Session::flash('ok', 'Set eliminado.');
    Response::redirect('/admin/sets');
  }

  // =========================
  // Helpers
  // =========================

  private function generateUniqueCode(\PDO $db): string
  {
    // Ej: 8 chars HEX -> "A1B2C3D4"
    for ($i = 0; $i < 20; $i++) {
      $code = strtoupper(bin2hex(random_bytes(4)));

      $st = $db->prepare("SELECT id FROM question_sets WHERE code=? LIMIT 1");
      $st->execute([$code]);
      if (!$st->fetch()) return $code;
    }

    // fallback
    return strtoupper(bin2hex(random_bytes(6)));
  }

  private function generateQrForSet(string $code): string
  {
    // Verifica clases (por si no cargó autoload o paquete faltante)
    if (!class_exists(Builder::class) || !class_exists(PngWriter::class)) {
      throw new \RuntimeException("Librería QR no disponible. Verifica vendor/autoload.php y endroid/qr-code:^5.");
    }

    // URL que se meterá dentro del QR.
    // Ajusta si tu ruta real para jugar por QR es otra.
    // Ejemplo: /play?code=XXXX
    $qrUrl = $this->absoluteUrl("/play?code=" . urlencode($code));

    // Carpeta destino
    $dir = __DIR__ . '/../../public/uploads/qrcodes';
    if (!is_dir($dir)) {
      if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
        throw new \RuntimeException("No se pudo crear carpeta: public/uploads/qrcodes");
      }
    }

    $filename = "set_" . $code . ".png";
    $fullPath = $dir . '/' . $filename;

    $result = Builder::create()
      ->writer(new PngWriter())
      ->data($qrUrl)
      ->size(320)
      ->margin(12)
      ->build();

    $result->saveToFile($fullPath);

    if (!is_file($fullPath)) {
      throw new \RuntimeException("No se pudo guardar el QR en disco.");
    }

    return $filename;
  }

  private function absoluteUrl(string $path): string
  {
    // base_url de config: /trivias/public
    $config  = require __DIR__ . '/../config/config.php';
    $baseUrl = rtrim($config['app']['base_url'] ?? '', '');

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $scheme  = $isHttps ? 'https' : 'http';
    $host    = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // $path debe iniciar con /
    if ($path === '' || $path[0] !== '/') $path = '/' . $path;

    return $scheme . '://' . $host . $baseUrl . $path;
  }
}
