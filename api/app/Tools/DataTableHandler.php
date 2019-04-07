<?php
declare(strict_types=1);

namespace App\Tools;

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

        $this->addSort($query);
        $this->addSearchFilter($query);

        return $query->paginate($this->pageSize, ['*'], 'page', floor($this->startAt / $this->pageSize));
    }

    private function addSort(Builder $query): void
    {
        foreach ($this->orders as $order) {
            $columnIdx = $order['column'];
            $dir = $order['dir'];

            $column = $this->columns[$columnIdx];
            if (filter_var($column['orderable'], FILTER_VALIDATE_BOOLEAN) === true) {
                $query->orderBy($column['name'], $dir);
            }
        }
    }

    private function addSearchFilter(Builder $query): void
    {
        $search = $this->search['value'];
        if ($search !== '') {
            foreach ($this->columns as $column) {
                if (filter_var($column['searchable'], FILTER_VALIDATE_BOOLEAN) === false) continue;

                $query->orWhere($column['name'], 'like', "%{$search}%");
            }
        }
    }
}