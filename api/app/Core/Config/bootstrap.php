<?php
declare(strict_types=1);

use Aura\Router\RouterContainer;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager;
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

$container['db'] = function (Container $container): Manager {
    $capsule = new Manager;
    $capsule->addConnection([
        'driver'    => 'mysql',
        'host'      => getenv('DB_HOST'),
        'database'  => getenv('DB_NAME'),
        'username'  => getenv('DB_USER'),
        'password'  => getenv('DB_PASS'),
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

return $container;