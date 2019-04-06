<?php
declare(strict_types=1);

use App\Domain\CompatibilityService\CompatibilityService;
use App\Domain\PickerWizard\Stage;
use App\Domain\PickerWizard\Wizard;
use Aura\Router\RouterContainer;
use DI\Container;
use DI\ContainerBuilder;
use function DI\factory;
use Dotenv\Dotenv;
use HansOtt\PSR7Cookies\RequestCookies;
use Illuminate\Database\Capsule\Manager;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

$dotenv = Dotenv::create(__DIR__);
$dotenv->load();

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    ServerRequestInterface::class => function () {
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );

        foreach ($request->getQueryParams() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        foreach ($request->getParsedBody() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $request;
    },
    RequestCookies::class => function (ServerRequestInterface $request) {
        return RequestCookies::createFromRequest($request);
    },
    ResponseInterface::class => function () {
        return new Response();
    },
    EmitterInterface::class => function () {
        return new SapiEmitter();
    },
    'router' => function () {
        return new RouterContainer();
    }
]);

$capsule = new Manager;
$capsule->getDatabaseManager()->extend('mongodb', function ($config, $name) {
    $config['name'] = $name;
    return new Jenssegers\Mongodb\Connection($config);
});

$capsule->addConnection([
    'driver' => 'mongodb',
    'host' => env('DB_HOST', 'localhost'),
    'port' => env('DB_PORT', 27017),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'options' => [
        'database' => 'admin' // sets the authentication database required by mongo 3
    ]
], 'mongodb');

$capsule->addConnection([
    'driver' => 'mongodb',
    'host' => env('DB_HOST', 'localhost'),
    'port' => env('DB_PORT', 27017),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'options' => [
        'database' => 'admin' // sets the authentication database required by mongo 3
    ]
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$containerBuilder->addDefinitions([
    'db' => function () use ($capsule) {
        return $capsule;
    },

    'logger' => function () {
        return new Logger('logger');
    },

    'stageIdx' => function (ServerRequestInterface $request) {
        return $request->getAttribute('stage', 0);
    },

    Stage::class => factory(function (int $nextStageIdx) {
        return new Stage($nextStageIdx);
    })->parameter('nextStageIdx', DI\get('stageIdx')),

    Wizard::class => factory(function (CompatibilityService $compatibilityService, Stage $stage, RequestCookies $cookies) {
        return new Wizard($compatibilityService, $stage, $cookies);
    })
]);

return $containerBuilder->build();