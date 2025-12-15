<?php
namespace App\Core;

final class Response {
  public static function redirect(string $path): void {
    $config = require __DIR__ . '/../config/config.php';
    $base = rtrim($config['app']['base_url'], '/');
    header("Location: " . $base . $path);
    exit;
  }

  public static function json(array $data, int $code=200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
  }
}
