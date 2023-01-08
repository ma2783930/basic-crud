<?php

namespace BasicCrud\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self prefix($prefix)
 * @method static self controller($controller)
 * @method static void register(callable $callback = null)
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
