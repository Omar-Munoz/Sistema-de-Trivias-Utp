<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Autoloader;
use App\Core\AppErrorHandler;
use App\Core\Router;
use App\Core\Session;

require_once __DIR__ . '/../app/core/Autoloader.php';
Autoloader::register();

$config = require __DIR__ . '/../app/config/config.php';

// Errores visibles (fase desarrollo)
$errorHandler = new AppErrorHandler((bool)$config['app']['debug']);
$errorHandler->register();

Session::start();

$routes = require __DIR__ . '/../app/config/routes.php';

$router = new Router($routes);
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
