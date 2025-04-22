<?php


namespace App\Providers;

use App\Models\Note;
use App\Models\User;
use App\Observers\NoteObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Services\QueryLoggerService;

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
        User::observe(UserObserver::class);
        Paginator::useBootstrap();
        app(QueryLoggerService::class)->boot();
        // if (config('app.debug') === true && config('logging.query.debug') === true) {
        //     DB::listen(function ($query) {
        //         File::append(
        //             storage_path('logs/query.log'),
        //             $query->sql . ' [' . implode(' ,', $query->bindings) . '] ' . PHP_EOL
        //         );
        //     });
        // }
    }
}
