<?php

namespace BasicCrud\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

/**
 * @property string $model
 * @property string $updateMessageKey
 * @method updateRules(Request $request, Model $model)
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
         * @var $modelObject \Illuminate\Database\Eloquent\Model
         */
        $model       = $this->model;
        $modelObject = $model::findOrFail($id);

        validator()
            ->make(
                $request->all(),
                method_exists($this, 'updateRules') ?
                    $this->updateRules($request, $modelObject) :
                    $this->getBaseTableRules($modelObject->getTable())
            )
            ->after(function (Validator $validator) use ($modelObject) {
                $this->withUpdateValidator($validator, $modelObject);
            })
            ->validate();

        DB::transaction(function () use ($request, $modelObject) {
            $modelObject->fill($request->all());
            $this->beforeUpdate($request, $modelObject);
            $modelObject->save();
            $this->afterUpdate($request, $modelObject);
        });

        return $this->withUpdateResponse();
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
     * @param \Illuminate\Validation\Validator    $validator
     * @param \Illuminate\Database\Eloquent\Model $modelObject
     * @return void
     */
    protected function withUpdateValidator(Validator $validator, Model $modelObject): void
    {
    }

    /**
     * @return array
     */
    protected function withUpdateResponse(): array
    {
        return [
            'message' => trans(property_exists($this, 'updateMessageKey') ?
                $this->updateMessageKey :
                'basic-crud::messages.updateSuccess')
        ];
    }
}
