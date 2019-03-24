<?php
declare(strict_types=1);

namespace App\Actions;


use App\Core\Action;
use App\Domain\PickerWizard\Stage;
use App\Tools\DataTableHandler;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FetchStagePartsAction extends Action
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $stageIdx = (int)$request->getAttribute('stage');

        $stage = new Stage();
        $model = $stage->buildDummyPart($stageIdx);

        $handler = DataTableHandler::fromRequest($request);
        $entities = $handler->fetchEntities($model->getTable());

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