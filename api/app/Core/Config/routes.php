<?php
declare(strict_types=1);

use App\Actions\IndexAction;
use Aura\Router\Map;

function load(Map $routes) {
    $routes->get('index', '/', new IndexAction());
}

