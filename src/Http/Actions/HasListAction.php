<?php

namespace BasicCrud\Http\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $model
 * @property string $resource
 * @property array $listColumns
 * @method Builder listDataQuery(Builder $builder)
 * @method array|string|int listDataMapper(Model $model, int $index)
 */
trait HasListAction
{
    private array $defaultColumns = ['id', 'name'];

    public function list()
    {
        /** @var $model Builder */
        $model   = $this->model;
        $columns = property_exists($this, 'listColumns') ? $this->listColumns : $this->defaultColumns;

        $listData = $model::select($columns);

        if (method_exists($this, 'listDataQuery')) {
            /** @var $query Builder */
            $query    = call_user_func([$this, 'listDataQuery'], $listData);
            $listData = $query->get();
        } else {
            $listData = $listData->get();
        }

        if (method_exists($this, 'listDataMapper')) {
            return $listData->map(function ($item, $index) {
                return call_user_func([$this, 'listDataMapper'], $item, $index);
            });
        }

        return $listData;
    }
}
