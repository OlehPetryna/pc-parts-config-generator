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

class WizardAction extends HtmlAction
{
    /**@var Wizard $wizard*/
    private $wizard;

    public function __construct(Wizard $wizard)
    {
        $this->wizard = $wizard->withStateParts();
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($this->wizard->endReached()) {
            return $response->withHeader('Location', '/summary');
        }

        /**@var PcPart $pickedModel*/
        $pickedModel = $this->wizard->buildStagePart()->newQuery()->find($request->getAttribute('partId'));

        $this->wizard->addPart($pickedModel);
        $response = $this->wizard->keepState($response);

        if ($this->wizard->endReached()) {
            return $response->withHeader('Location', '/summary');
        }

        $currentStepName = $this->wizard->getCurrentStepName();
        $totalStepsAmount = $this->wizard->getStepsCount();
        $currentStep = $this->wizard->getCurrentStepIdx();

        return $this->renderer()->render($response, '/wizard.php', [
            'currentStep' => $currentStep,
            'totalStepsAmount' => $totalStepsAmount,
            'stepName' => $currentStepName
        ]);
    }
}