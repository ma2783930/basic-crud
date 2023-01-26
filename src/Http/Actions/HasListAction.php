<?php

namespace BasicCrud\Http\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @mixin \Illuminate\Routing\Controller
 * @property string $model
 * @property string $resource
 * @property array  $listSelectColumns
 * @method Collection listDataQuery()
 * @method array|string|int listDataMapper(Model $model, int $index)
 */
trait HasListAction
{
    private array $defaultColumns = ['id', 'name'];
    public string $listSortColumn = '';

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function list(): Collection|array
    {
        /** @var $model Builder */
        $model = $this->model;

        if (method_exists($this, 'listDataQuery')) {
            /** @var $query Builder */
            return call_user_func([$this, 'listDataQuery']);
        } else {
            $columns  = property_exists($this, 'listSelectColumns') ? $this->listSelectColumns : $this->defaultColumns;
            $listData = $model::select($columns)
                              ->orderBy('name')
                              ->get();
        }

        if (method_exists($this, 'listDataMapper')) {
            return $listData->map(function ($item, $index) {
                return call_user_func([$this, 'listDataMapper'], $item, $index);
            });
        }

        return $listData;
    }
}
