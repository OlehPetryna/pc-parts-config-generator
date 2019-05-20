<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\Action;
use App\Domain\PickerWizard\Wizard;
use App\Domain\SuggestService\SuggestionCategories;
use App\Domain\SuggestService\SuggestService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class CompleteSuggestionAction extends Action
{
    /**@var Wizard $wizard */
    private $wizard;
    /**@var LoggerInterface */
    private $logger;

    public function __construct(Wizard $wizard, LoggerInterface $logger)
    {
        $this->wizard = $wizard;
        $this->logger = $logger;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $redirectUrl = null;
        try {
            $suggestionCategories = new SuggestionCategories($request->getAttribute('Question', []));

            $suggestionService = new SuggestService($this->wizard, $this->logger);
            $suggestionService->completeSuggestion($suggestionCategories);

            $response = $this->wizard->keepState($response);

            $redirectUrl = '/summary';
        } catch (\Throwable $e) {
            $this->logger->critical('[CompleteSuggestionAction] Received an error', ['error' => $e->getMessage()]);

            $response = $this->wizard->removeState($response);
            $redirectUrl = '/suggest?error=error';
        } finally {
            return $response->withHeader('Location', $redirectUrl);
        }

    }
}