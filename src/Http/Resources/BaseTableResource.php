<?php

namespace BasicCrud\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property integer        $id
 * @property string         $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
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
        if (method_exists($this->resource, 'getExpiredAtColumn')) {
            $extraFields[$this->resource->getExpiredAtColumn()] = $this->resource->getAttribute($this->resource->getExpiredAtColumn());
            $extraFields['is_expired'] = $this->resource->expired();
        }

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            ...$extraFields
        ];
    }
}
