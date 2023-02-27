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
                     Route::post('index', 'index');
                 }
                 if (method_exists(new $this->controller, 'search')) {
                     Route::get("search/{keyword}", 'search');
                 }
                 if (method_exists(new $this->controller, 'get')) {
                     Route::get("{id}", 'get');
                 }
                 if (method_exists(new $this->controller, 'store')) {
                     Route::post('/', 'store');
                 }
                 if (method_exists(new $this->controller, 'listItems')) {
                     Route::get('/', 'listItems');
                 }
                 if (method_exists(new $this->controller, 'update')) {
                     Route::put("{id}", 'update');
                 }
                 if (method_exists(new $this->controller, 'expire')) {
                     Route::put("{id}/expire", 'expire');
                 }
                 if (method_exists(new $this->controller, 'begin')) {
                     Route::put("{id}/begin", 'begin');
                 }
                 if (method_exists(new $this->controller, 'destroy')) {
                     Route::delete("{id}", 'destroy');
                 }

                 if (is_callable($callback)) {
                     call_user_func($callback);
                 }
             });
    }
}
