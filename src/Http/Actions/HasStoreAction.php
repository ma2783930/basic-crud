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
 * @property string $storeMessageKey
 */
trait HasStoreAction
{
    use WithBaseTableRules;

    /**
     * @param Request $request
     * @return array
     */
    public function store(Request $request): array
    {
        /** @var $modelObject Model */
        $model       = $this->model;
        $modelObject = new $model();

        Validator::make($request->all(), $this->getRules($request, $modelObject))
                 ->after(function (ValidatorInstance $validator) use ($request) {
                     $this->withStoreValidator($validator, $request);
                 })
                 ->validate();

        DB::transaction(function () use ($request, &$modelObject) {
            $modelObject->fill($request->all());

            $modelObject = $this->beforeStore($request, $modelObject);
            $modelObject->save();
            $modelObject = $this->afterStore($request, $modelObject);
        });

        return $this->withStoreResponse($modelObject);
    }

    /**
     * @param Request $request
     * @param Model   $model
     * @return Model
     */
    public function beforeStore(Request $request, Model $model): Model
    {
        return $model;
    }

    /**
     * @param Request $request
     * @param Model   $model
     * @return Model
     */
    public function afterStore(Request $request, Model $model): Model
    {
        return $model;
    }

    /**
     * @return array
     */
    protected function storeValidationRules(): array
    {
        return [];
    }

    /**
     * @param ValidatorInstance $validator
     * @param Request           $request
     * @return void
     */
    protected function withStoreValidator(ValidatorInstance $validator, Request $request): void
    {

    }

    /**
     * @param Model $model
     * @return array
     * @noinspection PhpUndefinedFunctionInspection
     */
    protected function withStoreResponse(Model $model): array
    {
        return [
            'message' => trans($this->storeMessageKey ?? 'basic-crud::messages.store-success')
        ];
    }
}
