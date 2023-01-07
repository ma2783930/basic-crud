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
    public function getBaseTableRules($table, $id = null): array
    {
        return [
            'name'       => [
                'required',
                'string',
                'min:3',
                'max:300',
                Rule::unique($table, 'name')
                    ->ignore($id)
                    ->where(function ($builder) {
                        $builder
                            ->whereNull('expired_at')
                            ->orWhere('expired_at', '>', Carbon::now());
                    })
            ],
            'expired_at' => 'nullable|date'
        ];
    }
}
