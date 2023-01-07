<?php

namespace BasicCrud\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @property string $model
 * @property string $deleteMessageKey
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
         * @var $modelObject \Illuminate\Database\Eloquent\Model
         */
        $model       = $this->model;
        $modelObject = $model::findOrFail($id);

        DB::transaction(function() use ($request, $modelObject) {
            $this->beforeDelete($request, $modelObject);
            $modelObject->delete();
            $this->afterDelete($request, $modelObject);
        });

        return $this->withDestroyResponse();
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function beforeDelete(Request $request, Model $model): void{}

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function afterDelete(Request $request, Model $model): void{}

    /**
     * @return array
     */
    protected function withDestroyResponse(): array
    {
        return [
            'message' => trans($this->deleteMessageKey ?? 'basic-crud::messages.deleteSuccess')
        ];
    }
}
