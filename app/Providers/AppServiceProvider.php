<?php

namespace App\Providers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Vite::prefetch(concurrency: 3);

        Gate::define('ticket.view', function ($user, Ticket $ticket) {
            return $user->isAdmin()
                || $user->isAgent()
                || $ticket->customer_id === $user->id;
        });

        Gate::define('ticket.reply', function ($user, Ticket $ticket) {
            return $user->isAdmin()
                || $user->isAgent()
                || $ticket->customer_id === $user->id;
        });

        Gate::define('ticket.updateStatus', function ($user, Ticket $ticket) {
            return $user->isAdmin() || $user->isAgent();
        });

        Gate::define('ticket.assign', function ($user, Ticket $ticket) {
            return $user->isAdmin() || $user->isAgent();
        });

        Gate::define('category.manage', function ($user) {
            return $user->isAdmin();
        });
    }
}
