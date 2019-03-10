<?php
declare(strict_types=1);

use App\Core\App;

require_once './vendor/autoload.php';

$app = new App();

$app->init();
$app->run();

