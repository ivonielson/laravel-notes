<?php


namespace App\Providers;
use App\Models\Note;
use App\Observers\NoteObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Note::observe(NoteObserver::class);
        Paginator::useBootstrap();
    }
}
