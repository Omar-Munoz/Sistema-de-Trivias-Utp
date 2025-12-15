<?php
namespace App\Core;

final class Autoloader {
  public static function register(): void {
    spl_autoload_register(function(string $class){
      if (str_starts_with($class, 'App\\')) {
        $path = __DIR__ . '/../' . str_replace(['App\\','\\'], ['', '/'], $class) . '.php';
        if (is_file($path)) require_once $path;
      }
    });
  }
}
