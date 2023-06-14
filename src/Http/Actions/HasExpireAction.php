<?php

namespace BasicCrud\Http\Actions;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @mixin \Illuminate\Routing\Controller
 * @property string  $model
 * @property string  $deleteMessageKey
 * @property boolean $tryDestroyOnExpire
 * @method Model findOne(Request $request, $id)
 */
trait HasExpireAction
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     * @return array
     */
    public function expire(Request $request, $id): array
    {
        /**
         * @var $model       \Illuminate\Database\Eloquent\Builder
         * @var $modelObject \Illuminate\Database\Eloquent\Model
         */
        $model       = $this->model;
        $modelObject = method_exists($this, 'findOne') ?
            call_user_func([$this, 'findOne'], $request, $id) :
            $model::findOrFail($id);

        if (method_exists($modelObject, 'getReadonlyColumn')) {
            abort_if(
                $modelObject->getAttribute($modelObject->getReadonlyColumn()),
                403,
                trans('basic-crud::messages.protected-record')
            );
        }

        DB::transaction(function () use ($request, $modelObject) {
            $this->beforeExpire($request, $modelObject);

            if ($this->tryDestroyOnExpire ?? false) {
                try {
                    $modelObject->forceDelete();
                } catch (Exception) {
                    $this->expireModel($modelObject);
                }
            } else {
                $this->expireModel($modelObject);
            }

            $this->afterExpire($request, $modelObject);
        });

        return $this->withExpireResponse($modelObject);
    }

    /**
     * @param $modelObject
     * @return void
     */
    private function expireModel($modelObject): void
    {
        $modelObject->forceFill([
            $modelObject->getExpiredAtColumn() => Carbon::now()
        ]);
        $modelObject->save();
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function beforeExpire(Request $request, Model $model): void
    {
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function afterExpire(Request $request, Model $model): void
    {
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    protected function withExpireResponse(Model $model): array
    {
        return [
            'message' => trans($this->deleteMessageKey ?? 'basic-crud::messages.expire-success')
        ];
    }
}
