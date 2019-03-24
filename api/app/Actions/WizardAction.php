<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\HtmlAction;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WizardAction extends HtmlAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $currentStep = $request->getAttribute('step', 0);

        return $this->renderer()->render($response, '/wizard.php', [
            'currentStep' => $currentStep,
            'totalStepsAmount' => 7,
            'stepName' => 'Motherboard'
        ]);
    }
}