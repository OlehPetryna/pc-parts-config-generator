<?php
declare(strict_types=1);

namespace App\Tools;

use App\Domain\PcParts\PcPart;
use App\Domain\PickerWizard\Wizard;
use Illuminate\Pagination\LengthAwarePaginator;
use Jenssegers\Mongodb\Eloquent\Builder;
use Psr\Http\Message\ServerRequestInterface;

class DataTableHandler
{
    private $columns;
    private $orders;
    private $startAt;
    private $pageSize;
    private $search;

    public function __construct(array $columns, array $orders, int $startAt, int $pageSize, array $search)
    {
        $this->columns = $columns;
        $this->orders = $orders;
        $this->startAt = $startAt;
        $this->pageSize = $pageSize;
        $this->search = $search;
    }

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $handler = new self(
            $request->getAttribute('columns', []),
            $request->getAttribute('order', []),
            (int)$request->getAttribute('start', 0),
            (int)$request->getAttribute('length', 15),
            $request->getAttribute('search', [])
        );

        return $handler;
    }

    public function fetchEntities(Wizard $wizard): LengthAwarePaginator
    {
        $query = $wizard->findCompatiblePartsQuery();

        $model = $wizard->buildStagePart();
        $modelSpecs = $this->buildSpecsColumns($model);

        $this->addSort($query);
        $this->addSearchFilter($query, $modelSpecs);

        $page = floor($this->startAt / $this->pageSize) + 1;
        return $query->paginate($this->pageSize, ['*'], 'page', $page);
    }

    private function addSort(Builder $query): void
    {
        foreach ($this->orders as $order) {
            $columnIdx = $order['column'];
            $dir = $order['dir'];

            $column = $this->columns[$columnIdx];
            if (filter_var($column['orderable'], FILTER_VALIDATE_BOOLEAN) === true) {
                $query->orderBy($column['data'], $dir);
            }
        }
    }

    private function addSearchFilter(Builder $query, array $additionalCols = []): void
    {
        $search = $this->search['value'];
        if ($search !== '') {
            $applicableColumns = array_filter($this->columns, function ($v) {
                return filter_var($v['searchable'], FILTER_VALIDATE_BOOLEAN) !== false;
            });

            $applicableColumns = array_merge($applicableColumns, $additionalCols);

            $query->whereNested(function (\Jenssegers\Mongodb\Query\Builder $query) use ($applicableColumns, $search) {
                foreach ($applicableColumns as $column) {
                    $query->orWhere($column['name'], 'like', "%{$search}%");
                }
            });
        }
    }

    private function buildSpecsColumns(PcPart $part): array
    {
        return array_map(function (string $specName) {
            return ['name' => "specifications.$specName.value"];
        }, $part->getAvailableSpecifications());
    }
}