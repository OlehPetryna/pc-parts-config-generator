<?php
declare(strict_types=1);

namespace App\Actions;


use Psr\Http\Message\RequestInterface;

class IndexAction
{
    public function __invoke(RequestInterface $request)
    {
        return 'Hello World!';
    }
}