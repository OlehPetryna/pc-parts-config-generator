<?php
declare(strict_types=1);

namespace App\Actions;


use App\Core\Action;
use App\Domain\PickerWizard\Stage;
use App\Domain\PickerWizard\Wizard;
use App\Tools\DataTableHandler;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FetchStagePartsAction extends Action
{
    private $wizard;

    public function __construct(Wizard $wizard)
    {
        $this->wizard = $wizard->withStateParts();
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $handler = DataTableHandler::fromRequest($request);
        $entities = $handler->fetchEntities($this->wizard);

        $response->getBody()->write(
            json_encode([
                'draw' => $request->getAttribute('draw'),
                'recordsTotal' => $entities->total(),
                'recordsFiltered' => $entities->total(),
                'data' => $entities->items()
            ])
        );

        return $response;
    }
}