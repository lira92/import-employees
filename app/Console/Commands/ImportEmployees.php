<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImportEmployeesService;

class ImportEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a csv file with an employees list';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ImportEmployeesService $importEmployeeService)
    {
        parent::__construct();
        $this->importEmployeeService = $importEmployeeService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->importEmployeeService->import();
        $this->info($response);
    }
}