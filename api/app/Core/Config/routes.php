<?php
declare(strict_types=1);

use App\Actions\CompleteSuggestionAction;
use App\Actions\FetchStagePartsAction;
use App\Actions\IndexAction;
use App\Actions\RewindWizardStepAction;
use App\Actions\SuggestAction;
use App\Actions\SummaryAction;
use App\Actions\WizardAction;
use Aura\Router\Map;
use DI\Container;

function load(Map $routes, Container $container) {
    $wrapAction = function ($actionName) use ($container){
        return function (...$params) use ($actionName, $container) {
            return $container->call($container->get($actionName), $params);
        };
    };

    $routes->get('index', '/', $wrapAction(IndexAction::class));

    $routes->route('wizard', '/wizard', $wrapAction(WizardAction::class));
    $routes->route('rewind-wizard-step', '/rewind-wizard-step', $wrapAction(RewindWizardStepAction::class));
    $routes->get('fetchStagePart', '/fetch-stage-parts/', $wrapAction(FetchStagePartsAction::class));

    $routes->get('suggest', '/suggest', $wrapAction(SuggestAction::class));

    $routes->post('complete-suggest', '/complete-suggestion', $wrapAction(CompleteSuggestionAction::class));

    $routes->get('summary', '/summary', $wrapAction(SummaryAction::class));
}