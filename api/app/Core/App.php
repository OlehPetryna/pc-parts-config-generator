<?php
declare(strict_types=1);

namespace App\Core;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

final class App
{
    /**@var Container $container*/
    private $container;

    public function init()
    {
        $this->container = require_once('Config/bootstrap.php');
        $this->loadRoutes();
    }

    public function run()
    {
        $router = $this->getRouter();
        $request = $this->getRequest();
        $response = $this->getResponse();
        $emitter = $this->getEmitter();

        $route = $router->getMatcher()->match($request);
        $request = $this->addRequestParameters($request, $route);
        $handler = $route->handler;

        $response = $this->container->call($handler, [ServerRequestInterface::class => $request]);
        $emitter->emit($response);
    }

    private function addRequestParameters(ServerRequestInterface $request, Route $route): ServerRequestInterface
    {
        foreach ($route->attributes as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        foreach ($request->getQueryParams() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        foreach ($request->getParsedBody() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $request;
    }

    private function loadRoutes(): void
    {
        require_once('Config/routes.php');
        load($this->getRouter()->getMap(), $this->container);
    }

    private function getRequest(): ServerRequestInterface
    {
        return $this->container->get(ServerRequestInterface::class);
    }

    private function getResponse(): ResponseInterface
    {
        return $this->container->get(ResponseInterface::class);
    }

    private function getEmitter(): SapiEmitter
    {
        return $this->container->get(EmitterInterface::class);
    }

    private function getRouter(): RouterContainer
    {
        return $this->container->get('router');
    }

    public function getLogger(): LoggerInterface
    {
        return $this->container->get('logger');
    }
}