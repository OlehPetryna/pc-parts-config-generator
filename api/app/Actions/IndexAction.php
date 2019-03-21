<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\HtmlAction;
use App\Domain\PcParts\Entities\CPU;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class IndexAction extends HtmlAction
{
    public function __invoke(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $cpus = CPU::query()->get();
        return $this->renderer()->render($response, '/index.php');
    }
}