<?php

namespace BasicCrud\Http\Actions;

use Carbon\Carbon;
use Illuminate\Validation\Rule;

trait WithBaseTableRules
{
    /**
     * @param $table
     * @param $id
     * @return array
     */
    public function getRules($table, $id = null): array
    {
        $model = new $this->model;
        $rules = [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:300',
                Rule::unique($table, 'name')
                    ->ignore($id)
                    ->where(function ($builder) use ($model) {
                        if (method_exists($model, 'getExpiredAtColumn')) {
                            $builder
                                ->whereNull($model->getExpiredAtColumn())
                                ->orWhere($model->getExpiredAtColumn(), '>', Carbon::now());
                        }
                    })
            ]
        ];

        if (method_exists($model, 'getExpiredAtColumn')) {
            $rules['expired_at'] = 'nullable|date';
        }

        return $rules;
    }
}
