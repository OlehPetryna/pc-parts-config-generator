<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\HtmlAction;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SuggestAction extends HtmlAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer()->render($response, '/suggest.php');
    }
}