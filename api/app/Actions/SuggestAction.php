<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\HtmlAction;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SuggestAction extends HtmlAction
{
    public function __invoke(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer()->render($response, '/suggest.php');
    }
}