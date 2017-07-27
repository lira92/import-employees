<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use App\Services\ImportEmployeesService;

class ImportEmployeesTest extends TestCase
{
    use DatabaseMigrations;

    public function __construct()
    {
        $this->importEmployeeService = new ImportEmployeesService(new Psr\Log\NullLogger());
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testInsertOneEmployee()
    {
        copy(__DIR__."/files/insert_employee.csv", __DIR__."/../storage/app/files_to_be_imported/insert_employee.csv");
        $company = factory(App\Company::class)->create();

        $this->importEmployeeService->import();

        Storage::delete('imported_files/insert_employee.csv');

        $this->seeInDatabase('employees', [
            'id' => 18294,
            'company_id' => 6854,
            'name' => 'JOÃO DOS SANTOS SILVA',
            'status' => 1
        ]);
    }
}
