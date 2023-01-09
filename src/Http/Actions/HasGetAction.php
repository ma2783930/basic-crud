<?php

namespace BasicCrud\Http\Actions;

use BasicCrud\Http\Resources\BaseTableResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * @mixin \Illuminate\Routing\Controller
 * @property string $model
 * @property string $singleResource
 * @method Model getOne(Request $request, $id)
 */
trait HasGetAction
{
    public function get(Request $request, $id): array
    {
        /**
         * @var $model \Illuminate\Database\Eloquent\Builder
         * @var $modelObject \Illuminate\Database\Eloquent\Model
         */
        $model       = $this->model;
        $modelObject = method_exists($this, 'getOne') ?
            call_user_func([$this, 'getOne'], $request, $id) :
            $model::findOrFail($id);

        $resource = $this->singleResource ?? BaseTableResource::class;

        return new $resource($modelObject);
    }
}
