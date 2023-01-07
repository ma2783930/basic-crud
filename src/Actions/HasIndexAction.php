<?php

namespace BasicCrud\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @property string $model
 * @property string $resource
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
            'perPage' => $perPage
        ] = $request->all();

        /** @var \Illuminate\Http\Resources\Json\JsonResource $resource */
        $resource = $this->resource;

        $paginatedData = $this->withIndexQuery($request)
                              ->paginate(
                                  $perPage ?? 10,
                                  "*",
                                  "page",
                                  $page ?? 1
                              );

        return $resource::collection($paginatedData);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function withIndexQuery(Request $request): Builder
    {
        [
            'sort_field'   => $sortField,
            'sort_order'   => $sortOrder,
            'quick_filter' => $quickFilter,
        ] = $request->all();

        $model = $this->model;

        return $model::applySort($sortField, $sortOrder)
                     ->applyQuickFilter($quickFilter);
    }
}
