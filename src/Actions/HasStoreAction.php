<?php

namespace BasicCrud\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

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
        validator()
            ->make(
                $request->all(),
                method_exists($this, 'storeRules') ?
                    $this->storeRules($request) :
                    $this->getBaseTableRules((new $this->model)->getTable())
            )
            ->after(function (Validator $validator) {
                $this->withStoreValidator($validator);
            })
            ->validate();

        DB::transaction(function() use ($request) {
            /**
             * @var $modelObject \Illuminate\Database\Eloquent\Model
             */
            $model = $this->model;

            $modelObject = new $model();
            $modelObject->fill($request->all());

            $this->beforeStore($request, $modelObject);
            $modelObject->save();
            $this->afterStore($request, $modelObject);
        });

        return $this->withStoreResponse();
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function beforeStore(Request $request, Model $model): void{}

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function afterStore(Request $request, Model $model): void{}

    /**
     * @return array
     */
    protected function storeValidationRules(): array
    {
        return [];
    }

    /**
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    protected function withStoreValidator(Validator $validator): void
    {
    }

    /**
     * @return array
     */
    protected function withStoreResponse(): array
    {
        return [
            'message' => trans($this->storeMessageKey ?? 'basic-crud::messages.storeSuccess')
        ];
    }
}
