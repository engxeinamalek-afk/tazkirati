<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\OrganizerPolicy;
use App\Services\SMSProvider\FakeSMSProvider;
use App\Services\SMSProvider\SMSProviderInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SMSProviderInterface::class, FakeSMSProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'organizer' => \App\Models\User::class,
            'venue' => \App\Models\Venue::class,
            'event' => \App\Models\Event::class,
        ]);
        Gate::policy(User::class, OrganizerPolicy::class);
        Route::bind('imageable', function ($value) {
            $type = request()->route('imageable_type');

            $morphMap = Relation::morphMap();

            if (array_key_exists($type, $morphMap)) {
                $modelClass = $morphMap[$type]; 
                return $modelClass::findOrFail($value);
            }

            abort(404, "The resource is not defined to this process.");
        });
    }
}
