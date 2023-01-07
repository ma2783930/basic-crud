<?php

namespace BasicCrud\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class BasicCrudRouteHelper
{
    private string $model;
    private string $prefix;
    private string $controller;
    private string $binding;

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
     * @param $model
     * @return $this
     */
    public function model($model): self
    {
        $this->model = $model;
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
     * @param $binding
     * @return $this
     */
    public function binding($binding): self
    {
        $this->binding = $binding;
        return $this;
    }

    /**
     * @return void
     */
    public function register()
    {
        $binding = $this->binding ?? Str::camel(class_basename($this->model));
        Route::prefix($this->prefix)
             ->name("{$this->prefix}.")
             ->controller($this->controller)
             ->group(function () use ($binding) {
                 if (method_exists(new $this->controller, 'index')) {
                     Route::post('index', 'index')->name('index');
                 }
                 if (method_exists(new $this->controller, 'store')) {
                     Route::post('/', 'store')->name('store');
                 }
                 if (method_exists(new $this->controller, 'list')) {
                     Route::get('/', 'list')->name('list');
                 }
                 if (method_exists(new $this->controller, 'update')) {
                     Route::put("{{$binding}}", 'update')->name('update');
                 }
                 if (method_exists(new $this->controller, 'destroy')) {
                     Route::delete("{{$binding}}", 'destroy')->name('destroy');
                 }
             });
    }
}
