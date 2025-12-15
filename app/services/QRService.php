<?php
namespace App\Services;

final class QRService {
  public function png(string $text): string {
    if (!class_exists(\Endroid\QrCode\QrCode::class)) {
      throw new \RuntimeException("QR requiere Composer: endroid/qr-code");
    }

    $qr = \Endroid\QrCode\QrCode::create($text);
    $writer = new \Endroid\QrCode\Writer\PngWriter();
    $result = $writer->write($qr);
    return $result->getString(); // bytes PNG
  }
}
