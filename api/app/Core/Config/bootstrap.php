<?php
declare(strict_types=1);

use Aura\Router\RouterContainer;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager;
use Monolog\Logger;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

$dotenv = Dotenv::create(__DIR__);
$dotenv->load();

$container = new Container();

$container['request'] = function (Container $container): RequestInterface {
    return ServerRequestFactory::fromGlobals(
        $_SERVER,
        $_GET,
        $_POST,
        $_COOKIE,
        $_FILES
    );
};

$container['response'] = function (Container $container): ResponseInterface {
    return new Response();
};

$container['emitter'] = function (Container $container): SapiEmitter {
    return new SapiEmitter();
};

$container['router'] = function (Container $container): RouterContainer {
    return new RouterContainer();
};

$capsule = new Manager;
$capsule->getDatabaseManager()->extend('mongodb', function ($config, $name) {
    $config['name'] = $name;
    return new Jenssegers\Mongodb\Connection($config);
});

$capsule->addConnection([
    'driver'   => 'mongodb',
    'host'     => env('DB_HOST', 'localhost'),
    'port'     => env('DB_PORT', 27017),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'options'  => [
        'database' => 'admin' // sets the authentication database required by mongo 3
    ]
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function (Container $container) use ($capsule) {
    return $capsule;
};

$container['logger'] = function (Container $container) {
    return new Logger('logger');
};


return $container;