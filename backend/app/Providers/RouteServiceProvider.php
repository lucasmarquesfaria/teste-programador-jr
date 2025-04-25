<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mapApiRoutes();
    }

    /**
     * Define the "api" routes for the application.
     */
    protected function mapApiRoutes(): void
    {
        Log::info('Carregando rotas do arquivo routes/api.php');

        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));
    }
}
