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
    $routes->get('index', '/', $container->get(IndexAction::class));

    $routes->route('wizard', '/wizard', $container->get(WizardAction::class));
    $routes->route('rewind-wizard-step', '/rewind-wizard-step', $container->get(RewindWizardStepAction::class));
    $routes->get('fetchStagePart', '/fetch-stage-parts/', $container->get(FetchStagePartsAction::class));

    $routes->get('suggest', '/suggest', new SuggestAction());
    $routes->post('complete-suggest', '/complete-suggestion', $container->get(CompleteSuggestionAction::class));

    $routes->get('summary', '/summary', $container->get(SummaryAction::class));
}