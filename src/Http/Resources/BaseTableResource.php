<?php

namespace BasicCrud\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property integer        $id
 * @property string         $name
 * @property boolean        $is_readonly
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $expired_at
 */
class BaseTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $extraFields = [];
        if (method_exists($this->resource, 'getReadonlyColumn')) {
            $extraFields[$this->resource->getReadonlyColumn()] = $this->resource->getAttribute($this->resource->getReadonlyColumn());
        }

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'expired_at' => $this->expired_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$extraFields
        ];
    }
}
