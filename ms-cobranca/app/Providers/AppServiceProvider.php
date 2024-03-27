<?php

namespace App\Providers;

use App\Services\Debt\UploadService;
use App\Contracts\IDebtInterface;
use App\Repository\DebtRepository;
use App\Services\Debt\DebtService;
use App\Contracts\ITicketInterface;
use App\Contracts\IUploadInterface;
use App\Contracts\IInvoiceInterface;
use App\Repository\TicketRepository;
use App\Services\ProcessDebtService;
use App\Services\Ticket\TicketService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\IProcessDebtInterface;
use App\Services\Invoice\InvoiceService;
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
        $this->app->bind(IUploadInterface::class, UploadService::class);
        $this->app->bind(IInvoiceInterface::class, InvoiceService::class);
        $this->app->bind(ITicketInterface::class, TicketService::class);
        $this->app->bind(IDebtRepositoryInterface::class, DebtRepository::class);
        $this->app->bind(ITicketRepositoryInterface::class, TicketRepository::class);
        $this->app->bind(IDebtInterface::class, DebtService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {}
}
