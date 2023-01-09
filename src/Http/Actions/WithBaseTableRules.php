<?php

namespace BasicCrud\Http\Actions;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @mixin \Illuminate\Routing\Controller
 */
trait WithBaseTableRules
{
    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array[]
     */
    public function getRules(Request $request, Model $model): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:300',
                Rule::unique($model->getTable(), 'name')
                    ->when($model->exists, function ($rule) use ($model) {
                        $rule->ignore($model->getKeyName());
                    })
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
