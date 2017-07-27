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
     * Test if one employee is inserted.
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

    /**
     * Test if the import doesn't stops with inconsistent rows.
     *
     * @return void
     */
    public function testInsertWithInconsistentRows()
    {
        copy(__DIR__."/files/inconsistent_employees.csv", __DIR__."/../storage/app/files_to_be_imported/inconsistent_employees.csv");
        $company = factory(App\Company::class)->create();

        $this->importEmployeeService->import();

        Storage::delete('imported_files/inconsistent_employees.csv');

        $this->seeInDatabase('employees', [
            'id' => 18297,
            'company_id' => 6854,
            'name' => 'JOÃO DOS SANTOS SILVA',
            'status' => 1
        ]);
    }

    /**
     * Test if the employee has inactivate
     *
     * @return void
     */
    public function testIfTheEmployeeHasInactivate()
    {
        copy(__DIR__."/files/inactivate_employee.csv", __DIR__."/../storage/app/files_to_be_imported/inactivate_employee.csv");
        $company = factory(App\Company::class)->create();
        $employee = factory(App\Employee::class)->make();
        var_dump($employee);
        die;

        $this->importEmployeeService->import();

        Storage::delete('imported_files/inactivate_employee.csv');

        $this->seeInDatabase('employees', [
            'id' => 18297,
            'company_id' => 6854,
            'name' => 'JOÃO DOS SANTOS SILVA',
            'status' => 0
        ]);
    }
}
