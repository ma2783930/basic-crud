<?php

namespace BasicCrud\Http\Actions;

use BasicCrud\Http\Resources\BaseTableResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @property string $model
 * @property string $resource
 * @property integer $pageSize
 * @method Builder getIndexQuery($quickFilter, $sortField, $sortOrder)
 */
trait HasIndexAction
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        [
            'page'    => $page,
            'per_page' => $perPage,
            'sort_field'   => $sortField,
            'sort_order'   => $sortOrder,
            'quick_filter' => $quickFilter,
        ] = $request->all();

        /**
         * @var \Illuminate\Http\Resources\Json\JsonResource $resource
         * @var \Illuminate\Contracts\Database\Eloquent\Builder $model
         */
        $resource = $this->resource ?? BaseTableResource::class;
        $model = $this->model;
        if (method_exists($this, 'getIndexQuery')) {
            /** @var $query \Illuminate\Contracts\Database\Eloquent\Builder */
            $query = call_user_func(
                [$this, 'getIndexQuery'],
                $quickFilter,
                $sortField ?? (property_exists($this, 'sort_field') ? $this->sort_field : ""),
                $sortOrder ?? (property_exists($this, 'sort_order') ? $this->sort_order : "")
            );
            $paginatedData = $query->paginate(
                $perPage ?? (property_exists($this, 'per_page') ? $this->pageSize : 10),
                "*",
                "page",
                $page ?? 1
            );
        } else {
            $paginatedData = $model::paginate(
                $perPage ?? (property_exists($this, 'per_page') ? $this->pageSize : 10),
                "*",
                "page",
                $page ?? 1
            );
        }

        return $resource::collection($paginatedData);
    }
}
