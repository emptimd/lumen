<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\Debugbar\LumenServiceProvider::class);
//            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
//            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
//            $this->app->register(\InfyOm\Generator\InfyOmGeneratorServiceProvider::class);
//            $this->app->register(\InfyOm\AdminLTETemplates\AdminLTETemplatesServiceProvider::class);
//            $this->app->register(\Laravel\Dusk\DuskServiceProvider::class);
        }
    }
}
