<?php
declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Action
{
    abstract public function __invoke(RequestInterface $request, ResponseInterface $response): ResponseInterface;
}