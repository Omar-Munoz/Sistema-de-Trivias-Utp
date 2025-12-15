<?php
return [
  'app' => [
    'name' => 'Sistema de Trivias UTP',
    'debug' => true, // en desarrollo: true (mostrar errores)
    'base_url' => '/trivias/public',
  ],
  'db' => [
    'host' => '127.0.0.1',
    'name' => 'trivias_utp',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4',
  ],
  'upload' => [
    'avatars_dir' => __DIR__ . '/../../public/uploads/avatars',
    'prizes_dir'  => __DIR__ . '/../../public/uploads/prizes',
    'max_mb' => 3
  ]
];
