<?php

namespace BasicCrud\Http\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorInstance;

/**
 * @mixin \Illuminate\Routing\Controller
 * @property string $model
 * @property string $updateMessageKey
 * @method Model findOne(Request $request, $id)
 */
trait HasUpdateAction
{
    use WithBaseTableRules;

    /**
     * @param Request                  $request
     * @param                          $id
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @noinspection PhpUndefinedFunctionInspection
     */
    public function update(Request $request, $id): array
    {
        /**
         * @var $model       \Illuminate\Database\Eloquent\Builder
         * @var $modelObject Model
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

        Validator::make($request->all(), $this->getRules($request, $modelObject))
                 ->after(function (ValidatorInstance $validator) use ($request, $modelObject) {
                     $this->withUpdateValidator($validator, $request, $modelObject);
                 })
                 ->validate();

        DB::transaction(function () use ($request, &$modelObject) {
            $modelObject->fill($request->all());
            $modelObject = $this->beforeUpdate($request, $modelObject);
            $modelObject->save();
            $modelObject = $this->afterUpdate($request, $modelObject);
        });

        return $this->withUpdateResponse($modelObject);
    }

    /**
     * @param Request $request
     * @param Model   $model
     * @return Model
     */
    public function beforeUpdate(Request $request, Model $model): Model
    {
        return $model;
    }

    /**
     * @param Request $request
     * @param Model   $model
     * @return Model
     */
    public function afterUpdate(Request $request, Model $model): Model
    {
        return $model;
    }

    /**
     * @param ValidatorInstance $validator
     * @param Request           $request
     * @param Model             $modelObject
     * @return void
     */
    protected function withUpdateValidator(ValidatorInstance $validator, Request $request, Model $modelObject): void
    {
    }

    /**
     * @param Model $model
     * @return array
     * @noinspection PhpUndefinedFunctionInspection
     */
    protected function withUpdateResponse(Model $model): array
    {
        return [
            'message' => trans(property_exists($this, 'updateMessageKey') ?
                $this->updateMessageKey :
                'basic-crud::messages.update-success')
        ];
    }
}
