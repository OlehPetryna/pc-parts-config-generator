<?php
declare(strict_types=1);

use App\Core\App;

require_once __DIR__ . '/vendor/autoload.php';

$app = new App();


$app->init();
$app->run();