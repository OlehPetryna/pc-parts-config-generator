<?php
declare(strict_types=1);

namespace App\Actions;

use App\Core\Action;
use App\Domain\PickerWizard\Wizard;
use App\Domain\SuggestService\SuggestionCategories;
use App\Domain\SuggestService\SuggestService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CompleteSuggestionAction extends Action
{
    /**@var Wizard $wizard*/
    private $wizard;

    public function __construct(Wizard $wizard)
    {
        $this->wizard = $wizard;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $suggestionCategories = new SuggestionCategories($request->getAttribute('Question', []));

        $suggestionService = new SuggestService($this->wizard);
        $suggestionService->completeSuggestion($suggestionCategories);

        $this->wizard->keepState($response);

        return $response->withHeader('Location', '/summary');
    }
}