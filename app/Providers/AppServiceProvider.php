<?php

namespace App\Providers;

use App\Services\EmployerFinancial\EmployerFinancialImpl;
use App\Services\EmployerFinancial\EmployerFinancialInterface;
use App\Services\Invoice\InvoiceImpl;
use App\Services\Invoice\InvoiceInterface;
use App\Services\WorkerFinancial\WorkerFinancialImpl;
use App\Services\WorkerFinancial\WorkerFinancialInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(EmployerFinancialInterface::class, EmployerFinancialImpl::class);
        $this->app->singleton(WorkerFinancialInterface::class, WorkerFinancialImpl::class);
        $this->app->singleton(InvoiceInterface::class, InvoiceImpl::class);
    }
}
