<?php

namespace BasicCrud\Http\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorInstance;

/**
 * @property string $model
 * @property string $updateMessageKey
 * @method updateRules(Request $request, Model $model)
 * @method Model findOne(Request $request, $id)
 */
trait HasUpdateAction
{
    use WithBaseTableRules;

    /**
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update(Request $request, $id): array
    {
        /**
         * @var $model \Illuminate\Database\Eloquent\Builder
         * @var $modelObject \Illuminate\Database\Eloquent\Model
         */
        $model       = $this->model;
        $modelObject = method_exists($this, 'findOne') ?
            call_user_func([$this, 'findOne'], $request, $id) :
            $model::findOrFail($id);

        Validator::make(
            $request->all(),
            method_exists($this, 'updateRules') ?
                call_user_func([$this, 'updateRules'], $request, $modelObject) :
                $this->getBaseTableRules((new $this->model)->getTable(), $modelObject->id)
        )
                 ->after(function (ValidatorInstance $validator) use ($request, $modelObject) {
                     $this->withUpdateValidator($validator, $request, $modelObject);
                 })
                 ->validate();

        DB::transaction(function () use ($request, $modelObject) {
            $modelObject->fill($request->all());
            $this->beforeUpdate($request, $modelObject);
            $modelObject->save();
            $this->afterUpdate($request, $modelObject);
        });

        return $this->withUpdateResponse($modelObject);
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function beforeUpdate(Request $request, Model $model): void
    {
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function afterUpdate(Request $request, Model $model): void
    {
    }

    /**
     * @param ValidatorInstance                   $validator
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $modelObject
     * @return void
     */
    protected function withUpdateValidator(ValidatorInstance $validator, Request $request, Model $modelObject): void
    {
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     * @noinspection PhpUndefinedFunctionInspection
     */
    protected function withUpdateResponse(Model $model): array
    {
        return [
            'message' => trans(property_exists($this, 'updateMessageKey') ?
                $this->updateMessageKey :
                'basic-crud::messages.updateSuccess')
        ];
    }
}
