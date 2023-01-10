<?php

namespace BasicCrud\Http\Actions;

use Illuminate\Http\Request;

/**
 * @mixin \Illuminate\Routing\Controller
 * @property string $model
 * @property string $searchResource
 * @method \Illuminate\Support\Collection searchQuery(Request $request, string $keyword)
 * @method array searchMapper(\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $results)
 */
trait HasSearchAction
{
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
        $model   = $this->model;
        $results = method_exists($this, 'searchQuery') ?
            call_user_func([$this, 'searchQuery'], $request, $keyword) :
            $model::select(['id', 'name'])
                  ->where('name', 'like', "%{$keyword}%")
                  ->get();

        if (method_exists($this, 'searchMapper')) {
            return call_user_func([$this, 'searchMapper'], $results);
        }

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
