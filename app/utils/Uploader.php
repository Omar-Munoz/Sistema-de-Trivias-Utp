<?php
namespace App\Utils;

final class Uploader {
  public static function image(array $file, string $destDir, int $maxMb=3): string {
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
      throw new \RuntimeException("Error subiendo archivo.");
    }

    if (!is_dir($destDir)) mkdir($destDir, 0777, true);

    $maxBytes = $maxMb * 1024 * 1024;
    if ($file['size'] > $maxBytes) throw new \RuntimeException("Archivo excede {$maxMb}MB.");

    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];

    if (!isset($allowed[$mime])) throw new \RuntimeException("Solo JPG/PNG/WEBP.");

    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $path = rtrim($destDir, '/') . '/' . $name;

    if (!move_uploaded_file($file['tmp_name'], $path)) {
      throw new \RuntimeException("No se pudo guardar el archivo.");
    }
    return $name; // se guarda en BD
  }
}
