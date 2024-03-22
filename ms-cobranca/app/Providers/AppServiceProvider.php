<?php

namespace App\Providers;

use App\Services\DebtService;
use App\Services\TicketService;
use App\Repository\DebtRepository;
use App\Repository\TicketRepository;
use App\Services\ProcessDebtService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\IProcessDebtInterface;
use App\Contracts\IDebtRepositoryInterface;
use App\Contracts\ITicketRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ProcessDebtService::class, function ($app) {
            $ticketService = $app->make(TicketService::class);
            $debtService = $app->make(DebtService::class);

            return new ProcessDebtService($ticketService, $debtService);
        });

        $this->app->bind(ITicketInterface::class, TicketService::class);
        $this->app->bind(IDebtRepositoryInterface::class, DebtRepository::class);
        $this->app->bind(ITicketRepositoryInterface::class, TicketRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {}
}
