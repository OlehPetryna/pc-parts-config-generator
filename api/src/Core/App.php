<?php
declare(strict_types=1);

namespace App\Core;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

final class App
{
    /**@var Container $container*/
    private $container;

    public function init()
    {
        $this->container = require_once './Config/bootstrap.php';
    }

    public function run()
    {
        $router = $this->getRouter();
        $request = $this->getRequest();
        $response = $this->getResponse();
        $emitter = $this->getEmitter();

        $route = $router->getMatcher()->match($request);
        $request = $this->addRequestParameters($request, $route);

        $response->getBody()->write($route->handler($request));
        $emitter->emit($response);
    }

    private function addRequestParameters(ServerRequestInterface $request, Route $route): ServerRequestInterface
    {
        foreach ($route->attributes as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $request;
    }

    private function getRequest(): ServerRequestInterface
    {
        return $this->container['request'];
    }

    private function getResponse(): ResponseInterface
    {
        return $this->container['response'];
    }

    private function getEmitter(): SapiEmitter
    {
        return $this->container['emitter'];
    }

    private function getRouter(): RouterContainer
    {
        return $this->container['router'];
    }
}