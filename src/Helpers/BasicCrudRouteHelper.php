<?php

namespace BasicCrud\Helpers;

use Illuminate\Support\Facades\Route;

class BasicCrudRouteHelper
{
    private string $prefix;
    private string $controller;

    /**
     * @param $prefix
     * @return $this
     */
    public function prefix($prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @param $controller
     * @return $this
     */
    public function controller($controller): self
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @param callable|null $callback
     * @return void
     */
    public function register(callable $callback = null): void
    {
        Route::prefix($this->prefix)
             ->name("{$this->prefix}.")
             ->controller($this->controller)
             ->group(function () use ($callback) {
                 if (method_exists(new $this->controller, 'index')) {
                     Route::post('index', 'index')->name('index');
                 }
                 if (method_exists(new $this->controller, 'search')) {
                     Route::get("search/{keyword}", 'search')->name('search');
                 }
                 if (method_exists(new $this->controller, 'get')) {
                     Route::get("{id}", 'get')->name('get');
                 }
                 if (method_exists(new $this->controller, 'store')) {
                     Route::post('/', 'store')->name('store');
                 }
                 if (method_exists(new $this->controller, 'list')) {
                     Route::get('/', 'list')->name('list');
                 }
                 if (method_exists(new $this->controller, 'update')) {
                     Route::put("{id}", 'update')->name('update');
                 }
                 if (method_exists(new $this->controller, 'expire')) {
                     Route::put("{id}/expire", 'expire')->name('expire');
                 }
                 if (method_exists(new $this->controller, 'begin')) {
                     Route::put("{id}/begin", 'begin')->name('begin');
                 }
                 if (method_exists(new $this->controller, 'destroy')) {
                     Route::delete("{id}", 'destroy')->name('destroy');
                 }

                 if (is_callable($callback)) {
                     call_user_func($callback);
                 }
             });
    }
}
