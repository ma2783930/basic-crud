<?php

namespace BasicCrud\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class BasicCrudRouteHelper
{
    private string $model;
    private string $prefix;
    private string $controller;

    public function prefix($prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function model($model): self
    {
        $this->model = $model;
        return $this;
    }

    public function controller($controller): self
    {
        $this->controller = $controller;
        return $this;
    }

    public function register()
    {
        $camelCaseModelName = Str::camel(class_basename($this->model));
        Route::prefix($this->prefix)
             ->name("{$this->prefix}.")
             ->controller($this->controller)
             ->group(function () use ($camelCaseModelName) {
                 Route::post('index', 'index')->name('index');
                 Route::post('/', 'store')->name('store');
                 Route::put("{{$camelCaseModelName}}", 'update')->name('update');
                 Route::delete("{{$camelCaseModelName}}", 'destroy')->name('destroy');
             });
    }
}
