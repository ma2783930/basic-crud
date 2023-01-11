<?php

namespace BasicCrud\Http\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * @mixin \Illuminate\Routing\Controller
 * @property string $model
 * @property string $searchResource
 * @property array  $searchSelectColumns
 * @property array  $searchColumns
 * @method \Illuminate\Support\Collection searchQuery(Request $request, string $keyword)
 */
trait HasSearchAction
{
    /**
     * @var string[]
     */
    private array $defaultSearchSelectColumns = ['id', 'name'];

    /**
     * @var string[]
     */
    private array $defaultSearchColumns = ['name'];

    /**
     * @param \Illuminate\Http\Request $request
     * @param                          $keyword
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Support\Collection|array
     */
    public function search(Request $request, $keyword): \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Support\Collection|array
    {
        /**
         * @var $model       \Illuminate\Database\Eloquent\Builder
         * @var $modelObject \Illuminate\Database\Eloquent\Model
         * @var $resource    \Illuminate\Http\Resources\Json\JsonResource
         */
        $model = $this->model;

        if (method_exists($this, 'searchQuery')) {
            return call_user_func([$this, 'searchQuery'], $request, $keyword);
        }

        $results = $model::select(property_exists($this, 'searchSelectColumns') ? $this->searchSelectColumns : $this->defaultSearchSelectColumns)
                         ->where(function (Builder $builder) use ($keyword) {
                             foreach ($this->searchColumns ?? $this->defaultSearchColumns as $column) {
                                 $builder->where($column, 'like', "%{$keyword}%");
                             }
                         })
                         ->get();

        if (property_exists($this, 'searchResource')) {
            $resource = $this->searchResource;
            return $resource::collection($results);
        }

        return $results
            ->map(function ($record) {
                /** @var $record \BasicCrud\Contracts\BaseModelContract */
                return [
                    'id'   => $record->id,
                    'name' => $record->name
                ];
            })
            ->toArray();
    }
}
