<?php
declare(strict_types=1);

use App\Actions\FetchStagePartsAction;
use App\Actions\IndexAction;
use App\Actions\SuggestAction;
use App\Actions\WizardAction;
use Aura\Router\Map;
use DI\Container;

function load(Map $routes, Container $container) {
    $routes->get('index', '/', $container->get(IndexAction::class));
    $routes->route('wizard', '/wizard', $container->get(WizardAction::class));
    $routes->get('suggest', '/suggest', new SuggestAction());
    $routes->get('fetchStagePart', '/fetch-stage-parts/', new FetchStagePartsAction());
}