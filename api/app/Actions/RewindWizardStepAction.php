<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\Action;
use App\Core\HtmlAction;
use App\Domain\PcParts\PcPart;
use App\Domain\PickerWizard\Stage;
use App\Domain\PickerWizard\Wizard;
use DI\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RewindWizardStepAction extends Action
{
    /**@var Wizard $wizard*/
    private $wizard;

    public function __construct(Wizard $wizard)
    {
        $this->wizard = $wizard->withStateParts();
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->wizard->rewindOneStep();
        $response = $this->wizard->keepState($response);

        return $response->withHeader('Location', '/wizard');
    }
}