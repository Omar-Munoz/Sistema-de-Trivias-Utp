<?php
namespace App\Core;

final class AppErrorHandler implements ErrorHandlerInterface {
  public function __construct(private bool $debug) {}

  public function register(): void {
    ini_set('display_errors', $this->debug ? '1' : '0');
    error_reporting($this->debug ? E_ALL : 0);

    set_exception_handler([$this, 'handleException']);
    set_error_handler([$this, 'handleError']);
  }

  public function handleException(\Throwable $e): void {
    http_response_code(500);
    if ($this->debug) {
      echo "<h1>Error</h1>";
      echo "<pre>" . htmlspecialchars((string)$e) . "</pre>";
      return;
    }
    echo "Ocurrió un error. Intente más tarde.";
  }

  public function handleError(int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) return false;
    throw new \ErrorException($message, 0, $severity, $file, $line);
  }
}
