<?php

namespace App\Providers;

use App\Models\Setting;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // 1. Tambahkan ini
use Spatie\Activitylog\Models\Activity; // 2. Tambahkan ini
use App\Policies\ActivityPolicy; // 3. Tambahkan ini

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
        Gate::policy(Activity::class, ActivityPolicy::class);
        Scramble::configure()
        ->withDocumentTransformers(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });
        View::share('site_settings', Setting::all()->pluck('value', 'key'));


        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            
            $dbUrl = Setting::get('site_url');
            $siteName = Setting::get('site_name');

            if ($dbUrl) {
                config(['app.url' => $dbUrl]);
                \Illuminate\Support\Facades\URL::forceRootUrl($dbUrl);
            }

            if ($siteName) {
                config(['app.name' => $siteName]);
            }
        }
    }
}
