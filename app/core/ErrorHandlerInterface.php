<?php
namespace App\Core;

interface ErrorHandlerInterface {
  public function register(): void;
  public function handleException(\Throwable $e): void;
  public function handleError(int $severity, string $message, string $file, int $line): bool;
}
