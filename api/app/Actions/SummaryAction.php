<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\HtmlAction;
use App\Domain\PcParts\PcPart;
use App\Domain\PickerWizard\Stage;
use App\Domain\PickerWizard\Wizard;
use DI\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SummaryAction extends HtmlAction
{
    /**@var Wizard $wizard*/
    private $wizard;

    public function __construct(Wizard $wizard)
    {
        $this->wizard = $wizard->withStateParts();
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $partsCollection = $this->wizard->getStateParts();

        return $this->renderer()->render($response, '/summary.php', [
            'parts' => $partsCollection
        ]);
    }
}