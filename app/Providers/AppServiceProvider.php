<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\LeaveRequest;
use App\Policies\LeaveRequestPolicy;

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
        LeaveRequest::class => LeaveRequestPolicy::class,
    ];
    
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
        //
    }
}
