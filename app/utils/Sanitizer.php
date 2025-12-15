<?php
namespace App\Utils;

final class Sanitizer {
  public static function str(?string $v): string {
    $v = (string)$v;
    $v = trim($v);
    $v = strip_tags($v);
    return $v;
  }

  public static function email(?string $v): string {
    return filter_var(self::str($v), FILTER_SANITIZE_EMAIL) ?: '';
  }

  public static function int(mixed $v): int {
    return (int)filter_var($v, FILTER_SANITIZE_NUMBER_INT);
  }
}
