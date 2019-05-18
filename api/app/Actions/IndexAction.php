<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\HtmlAction;
use App\Domain\PcParts\Entities\CPU;
use App\Domain\PickerWizard\Wizard;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexAction extends HtmlAction
{
    private $wizard;

    public function __construct(Wizard $wizard)
    {
        $this->wizard = $wizard;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer()->render($this->wizard->removeState($response), '/index.php');
    }
}