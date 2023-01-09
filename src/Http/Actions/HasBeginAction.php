<?php

namespace BasicCrud\Http\Actions;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @mixin \Illuminate\Routing\Controller
 * @property string $model
 * @property string $deleteMessageKey
 * @method Model findOne(Request $request, $id)
 */
trait HasBeginAction
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     * @return array
     */
    public function begin(Request $request, $id): array
    {
        /**
         * @var $model       \Illuminate\Database\Eloquent\Builder
         * @var $modelObject \Illuminate\Database\Eloquent\Model
         */
        $model       = $this->model;
        $modelObject = method_exists($this, 'findOne') ?
            call_user_func([$this, 'findOne'], $request, $id) :
            $model::findOrFail($id);

        DB::transaction(function () use ($request, $modelObject) {
            $this->beforeBegin($request, $modelObject);
            $modelObject->forceFill([
                $modelObject->getBegindAtColumn() => null
            ]);
            $modelObject->save();
            $this->afterBegin($request, $modelObject);
        });

        return $this->withBeginResponse($modelObject);
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function beforeBegin(Request $request, Model $model): void
    {
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function afterBegin(Request $request, Model $model): void
    {
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     * @noinspection PhpUndefinedFunctionInspection
     */
    protected function withBeginResponse(Model $model): array
    {
        return [
            'message' => trans($this->deleteMessageKey ?? 'basic-crud::messages.expire-success')
        ];
    }
}
