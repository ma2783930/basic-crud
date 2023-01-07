<?php

namespace BasicCrud\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method self prefix($prefix)
 * @method self model($model)
 * @method self controller($controller)
 * @method self binding($binding)
 * @method void register()
 */
class BasicCrudRoutes extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'basicCrudRouteHelper';
    }
}
