<?php
namespace App\Utils;

final class Time {
  public static function now(): string { return date('Y-m-d H:i:s'); }
}
