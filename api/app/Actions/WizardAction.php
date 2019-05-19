<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\HtmlAction;
use App\Domain\CompatibilityService\CompatibilityContext;
use App\Domain\PcParts\PcPart;
use App\Domain\PickerWizard\Stage;
use App\Domain\PickerWizard\Wizard;
use DI\Container;
use HansOtt\PSR7Cookies\RequestCookies;
use HansOtt\PSR7Cookies\ResponseCookies;
use HansOtt\PSR7Cookies\SetCookie;
use http\Env\Request;
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
        if ($request->getAttribute('refresh')) {
            return $this->wizard->removeState($response)->withHeader('Location', '/wizard');
        }

        if ($this->wizard->endReached()) {
            return $response->withHeader('Location', '/summary');
        }

        /**@var PcPart $pickedModel*/
        $pickedModel = $this->wizard->buildStagePart()->newQuery()->find($request->getAttribute('partId'));

        $this->wizard->addPart($pickedModel);

        $response = $this->wizard->keepState($this->keepCompatibilityContext($request, $response));

        if ($this->wizard->endReached()) {
            return $response->withHeader('Location', '/summary');
        }

        $currentStepName = $this->translateStepName($this->wizard->getCurrentStepName());
        $totalStepsAmount = $this->wizard->getStepsCount();
        $currentStep = $this->wizard->getCurrentStepIdx();
        $pickedParts = $this->wizard->getStateParts();
        $showWithoutPrices = $this->showWithoutPrices($request);

        return $this->renderer()->render($response, '/wizard.php', [
            'currentStep' => $currentStep,
            'totalStepsAmount' => $totalStepsAmount,
            'stepName' => $currentStepName,
            'pickedParts' => $pickedParts,
            'showWithoutPrices' => $showWithoutPrices
        ]);
    }

    private function translateStepName(string $name): string
    {
        $translations = [
            "Motherboard" => 'Материнську плату',
            "CPU" => 'Процесор',
            "VideoCard" => 'Відеокарту',
            "RAM" => 'Оперативну пам\'ять',
            "Storage" => 'Сховище даних',
            "PowerSupply" => 'Блок живлення',
            "PcCase" => 'Корпус',
        ];

        return $translations[$name];
    }

    private function keepCompatibilityContext(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return SetCookie::thatExpires(
            CompatibilityContext::SHOW_WITHOUT_PRICE_COOKE,
            $this->showWithoutPrices($request) ? '1' : '0',
            new \DateTime('tomorrow')
        )->addToResponse($response);
    }

    private function showWithoutPrices(ServerRequestInterface $request): bool
    {
        $cookie = RequestCookies::createFromRequest($request);
        $show = $request->getAttribute('showWithoutPrices', 0);

        return (bool)$show;
    }
}