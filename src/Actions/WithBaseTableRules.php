<?php

namespace BasicCrud\Actions;

use Illuminate\Validation\Rule;

trait WithBaseTableRules
{
    public function getBaseTableRules($table, $id = null): array
    {
        return [
            'name'       => [
                'required',
                'string',
                'min:3',
                'max:300',
                Rule::unique($table, 'name')
                    ->where(
                        fn($builder) => $builder->whereNull('expired_at')
                                                ->orWhere('expired_at', '>', now())
                    )
            ],
            'expired_at' => 'nullable|date'
        ];
    }
}
