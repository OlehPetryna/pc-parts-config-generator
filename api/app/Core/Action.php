<?php
declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Action
{
    abstract public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}