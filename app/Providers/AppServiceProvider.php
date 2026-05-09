<?php

namespace App\Providers;

use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        FileUpload::configureUsing(function (FileUpload $fileUpload): void {
            $fileUpload
                ->disk('s3')
                ->visibility('public')
                ->fetchFileInformation(false);
        });
    }
}
