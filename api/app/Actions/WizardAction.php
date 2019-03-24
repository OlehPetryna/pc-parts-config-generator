<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\HtmlAction;
use App\Domain\PickerWizard\Stage;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WizardAction extends HtmlAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $stage = new Stage();

        $currentStep = (int)$request->getAttribute('stage', 0);
        $currentStepName = $stage->getStageName($currentStep);
        $totalStepsAmount = $stage->getStagesCount();

        return $this->renderer()->render($response, '/wizard.php', [
            'currentStep' => $currentStep,
            'totalStepsAmount' => $totalStepsAmount,
            'stepName' => $currentStepName
        ]);
    }
}