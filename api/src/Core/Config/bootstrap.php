<?php
declare(strict_types=1);

use Aura\Router\RouterContainer;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

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

$container['request'] = function (Container $container): ResponseInterface {
    return new Response();
};

$container['emitter'] = function (Container $container): SapiEmitter {
    return new SapiEmitter();
};

$container['router'] = function (Container $container): RouterContainer {
    return new RouterContainer();
};

return $container;