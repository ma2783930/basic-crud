<?php

namespace BasicCrud\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property integer        $id
 * @property string         $name
 * @property \Carbon\Carbon $expiredAt
 * @property \Carbon\Carbon $createdAt
 * @property \Carbon\Carbon $updatedAt
 */
class BaseTablesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'expiredAt' => $this->expiredAt,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
