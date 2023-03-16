<?php

namespace BasicCrud\Http\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @mixin \Illuminate\Routing\Controller
 * @property string $model
 * @property string $deleteMessageKey
 * @method Model findOne(Request $request, $id)
 */
trait HasDestroyAction
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     * @return array
     */
    public function destroy(Request $request, $id): array
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

        DB::beginTransaction();

        try {
            $this->beforeDelete($request, $modelObject);
            $modelObject->delete();
            $this->afterDelete($request, $modelObject);

            DB::commit();

            return $this->withDestroyResponse($modelObject);
        } catch (\Exception $exception) {
            DB::rollBack();

            abort(400, trans('basic-crud::messages.delete-not-possible'));
        }
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function beforeDelete(Request $request, Model $model): void
    {
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function afterDelete(Request $request, Model $model): void
    {
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    protected function withDestroyResponse(Model $model): array
    {
        return [
            'message' => trans($this->deleteMessageKey ?? 'basic-crud::messages.delete-success')
        ];
    }
}
