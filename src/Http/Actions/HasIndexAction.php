<?php

namespace BasicCrud\Http\Actions;

use BasicCrud\Http\Resources\BaseTableResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @mixin \Illuminate\Routing\Controller
 * @property string  $model
 * @property string  $resource
 * @property integer $pageSize
 * @property array $indexActionRelationships
 * @method Builder getIndexQuery($quickFilter, $sortField, $sortOrder)
 */
trait HasIndexAction
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @noinspection PhpUndefinedMethodInspection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        [
            'page'         => $page,
            'per_page'     => $perPage,
            'sort_field'   => $sortField,
            'sort_order'   => $sortOrder,
            'quick_filter' => $quickFilter,
        ] = $request->all();

        /**
         * @var $resource \Illuminate\Http\Resources\Json\JsonResource
         * @var $model    Builder
         */
        $resource    = $this->resource ?? BaseTableResource::class;
        $model       = $this->model;
        if (method_exists($this, 'getIndexQuery')) {
            /** @var $query \Illuminate\Contracts\Database\Eloquent\Builder */
            $query         = call_user_func(
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
            $paginatedData = $model::with(property_exists($this, 'indexActionRelationships') ? $this->indexActionRelationships : [])
                                   ->when(
                                       fn(Builder $builder) => method_exists($builder->getModel(), 'getExpiredAtColumn'),
                                       fn(Builder $builder) => $builder->withExpired()
                                   )
                                   /*->when(
                                       fn(Builder $builder) => method_exists($builder->getModel(), 'getReadonlyColumn'),
                                       fn(Builder $builder) => $builder->orderBy($builder->getModel()->getReadonlyColumn())
                                   )*/
                                   ->when(
                                       fn(Builder $builder) => method_exists($builder->getModel(), 'scopeApplySort'),
                                       fn(Builder $builder) => $builder->applySort($sortField, $sortOrder)
                                   )
                                   ->when(
                                       fn(Builder $builder) => method_exists($builder->getModel(), 'scopeApplyQuickFilter'),
                                       fn(Builder $builder) => $builder->applyQuickFilter($quickFilter)
                                   )
                                   ->paginate(
                                       $perPage ?? (property_exists($this, 'per_page') ? $this->pageSize : 10),
                                       "*",
                                       "page",
                                       $page ?? 1
                                   );
        }

        return $resource::collection($paginatedData);
    }
}
