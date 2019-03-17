<?php
declare(strict_types=1);

use App\Actions\IndexAction;
use App\Actions\SuggestAction;
use App\Actions\WizardAction;
use Aura\Router\Map;

function load(Map $routes) {
    $routes->get('index', '/', new IndexAction());
    $routes->get('wizard', '/wizard', new WizardAction());
    $routes->get('suggest', '/suggest', new SuggestAction());
}