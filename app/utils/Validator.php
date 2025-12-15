<?php
namespace App\Utils;

final class Validator {
  public static function required(string $v): bool { return trim($v) !== ''; }
  public static function minLen(string $v, int $n): bool { return mb_strlen(trim($v)) >= $n; }
  public static function isEmail(string $v): bool { return (bool)filter_var($v, FILTER_VALIDATE_EMAIL); }
}
