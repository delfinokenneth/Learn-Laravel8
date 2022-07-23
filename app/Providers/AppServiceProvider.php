<?php

namespace App\Providers;

use App\Http\ViewComposers\ActivityComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Blade::aliasComponent('components.badge', 'badge');
        Blade::aliasComponent('components.updated', 'updated');
        Blade::aliasComponent('components.card', 'card');
        Blade::aliasComponent('components.tags', 'tags');
        Blade::aliasComponent('components.tags', 'tags');
        Blade::aliasComponent('components.errors', 'errors');
        Blade::aliasComponent('components.comment-form', 'commentForm');
        Blade::aliasComponent('components.comment-list', 'commentList');
        view()->composer(['posts.index', 'posts.show'], ActivityComposer::class);
    }
}
