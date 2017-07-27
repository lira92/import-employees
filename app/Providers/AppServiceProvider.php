<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Services\ImportEmployeesService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(ImportEmployeesService::class)
            ->needs(\Psr\Log\LoggerInterface::class)
            ->give(function () {
                $stream = new StreamHandler(storage_path("/logs/import_employees.log"), Logger::DEBUG);
                $logger = new Logger('EmployeesImport');
                $logger->pushHandler($stream);
                return $logger;
            });
    }
}
