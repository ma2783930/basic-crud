<?php

namespace BasicCrud\Http\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorInstance;

/**
 * @property string $model
 * @property string $storeMessageKey
 * @method storeRules(Request $request)
 */
trait HasStoreAction
{
    use WithBaseTableRules;

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function store(Request $request): array
    {
        Validator::make(
            $request->all(),
            method_exists($this, 'storeRules') ?
                call_user_func([$this, 'storeRules'], $request) :
                $this->getBaseTableRules((new $this->model)->getTable())
        )
                 ->after(function (ValidatorInstance $validator) use ($request) {
                     $this->withStoreValidator($validator, $request);
                 })
                 ->validate();

        /** @var $modelObject \Illuminate\Database\Eloquent\Model */
        $model       = $this->model;
        $modelObject = new $model();

        DB::transaction(function () use ($modelObject, $request) {
            $modelObject->fill($request->all());

            $this->beforeStore($request, $modelObject);
            $modelObject->save();
            $this->afterStore($request, $modelObject);
        });

        return $this->withStoreResponse($modelObject);
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function beforeStore(Request $request, Model $model): void
    {
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function afterStore(Request $request, Model $model): void
    {
    }

    /**
     * @return array
     */
    protected function storeValidationRules(): array
    {
        return [];
    }

    /**
     * @param ValidatorInstance        $validator
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function withStoreValidator(ValidatorInstance $validator, Request $request): void
    {

    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
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
