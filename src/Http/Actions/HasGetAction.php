<?php

namespace BasicCrud\Http\Actions;

use BasicCrud\Http\Resources\BaseTableResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Illuminate\Routing\Controller
 * @property string $model
 * @property string $singleResource
 * @property string $resource
 * @property array $getActionRelationships
 * @method Model getOne(Request $request, $id)
 */
trait HasGetAction
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     * @return array|\Illuminate\Http\Resources\Json\JsonResource
     * @noinspection PhpUndefinedMethodInspection
     */
    public function get(Request $request, $id): array|JsonResource
    {
        /**
         * @var \Illuminate\Database\Eloquent\Builder $model
         * @var \Illuminate\Database\Eloquent\Model $modelObject
         */
        $model       = $this->model;
        $modelObject = method_exists($this, 'getOne') ?
            call_user_func([$this, 'getOne'], $request, $id) :
            $model::with(property_exists($this, 'getActionRelationships') ? $this->getActionRelationships : [])
                  ->when(method_exists($modelObject, 'getExpiredAtColumn'), fn(Builder $builder) => $builder->withExpired())
                  ->findOrFail($id);

        $resource = $this->singleResource ?? $this->resource ?? BaseTableResource::class;

        return new $resource($modelObject);
    }
}
