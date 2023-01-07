<?php

namespace BasicCrud\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self prefix($prefix)
 * @method static self model($model)
 * @method static self controller($controller)
 * @method static self binding($binding)
 * @method static void register()
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
