<?php
namespace App\Core;

final class Router {
  public function __construct(private array $routes) {}

  public function dispatch(string $method, string $uri): void {
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';

    // Detecta el prefijo real donde corre index.php, ej: /trivias/public
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    if ($scriptDir === '') $scriptDir = '';

    // Si el path comienza con ese prefijo, lo recortamos
    if ($scriptDir !== '' && str_starts_with($path, $scriptDir)) {
      $path = substr($path, strlen($scriptDir));
      if ($path === '') $path = '/';
    }

    // Normaliza slash final (excepto raíz)
    if ($path !== '/' && str_ends_with($path, '/')) {
      $path = rtrim($path, '/');
    }

    foreach ($this->routes as $r) {
      [$m, $p, $action] = $r;

      if ($m === $method && $p === $path) {
        $this->call($action);
        return;
      }
    }

    http_response_code(404);
    echo "404 - Ruta no encontrada (" . htmlspecialchars($path) . ")";
  }

  private function call(string $action): void {
    [$controller, $method] = explode('@', $action);

    $class = "App\\Controllers\\{$controller}";
    if (!class_exists($class)) {
      throw new \RuntimeException("No existe controlador: $class");
    }

    $obj = new $class();

    if (!method_exists($obj, $method)) {
      throw new \RuntimeException("No existe método: {$controller}@{$method}");
    }

    $obj->$method();
  }
}
