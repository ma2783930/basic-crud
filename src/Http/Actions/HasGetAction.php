<?php

namespace BasicCrud\Http\Actions;

use BasicCrud\Http\Resources\BaseTableResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Illuminate\Routing\Controller
 * @property string $model
 * @property string $singleResource
 * @property string $resource
 * @method Model getOne(Request $request, $id)
 */
trait HasGetAction
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return array|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function get(Request $request, $id): array|JsonResource
    {
        /**
         * @var $model \Illuminate\Database\Eloquent\Builder
         * @var $modelObject \Illuminate\Database\Eloquent\Model
         */
        $model       = $this->model;
        $modelObject = method_exists($this, 'getOne') ?
            call_user_func([$this, 'getOne'], $request, $id) :
            $model::findOrFail($id);

        $resource = $this->singleResource ?? $this->resource ?? BaseTableResource::class;

        return new $resource($modelObject);
    }
}
