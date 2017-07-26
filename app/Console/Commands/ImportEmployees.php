<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Employee;
use App\Company;
use Illuminate\Support\Facades\Storage;
use Excel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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

    private $column_indexes = [
        'employee_id' => 1,
        'company_id' => 0,
        'name' => 2,
        'processed_at' => 3,
        'operation' => 4
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = Storage::files("files_to_be_imported");
        
        if(count($files) == 0) {
            $this->info("Nenhum arquivo para ser importado");
            return;
        }
        $stream = new StreamHandler(storage_path("/logs/import_employees.log"), Logger::DEBUG);
        $logger = new Logger('EmployeesImport');
        $logger->pushHandler($stream);

        foreach($files as $file) {
            $data = Excel::load(Storage::url('app/'.$file), function($reader) {
            }, 'ISO 8859-1')->get()->toArray();

            if(!empty($data) && count($data) > 0) {
                foreach ($data as $key => $value) {
                    try {
                        if(Company::find($value[$this->column_indexes['company_id']]) == null) {
                            throw new \Exception("Empresa {$value[0]} não encontrada");
                        }
                        if($value[$this->column_indexes['name']] == null) {
                            throw new \Exception("Nome do Funcionário não definido");
                        }
                        if($value[$this->column_indexes['processed_at']] == null) {
                            throw new \Exception("Data de processamento não definida");
                        }

                        if($value[$this->column_indexes['operation']] == 'I') {
                            $employee = new Employee();
                            $employee->id = $value[$this->column_indexes['employee_id']];
                            $employee->company_id = $value[$this->column_indexes['company_id']];
                            $employee->name = $value[$this->column_indexes['name']];
                            $employee->processed_at = $this->convertDate($value[$this->column_indexes['processed_at']]);
                            $employee->save();
                        }
                        else if($value[$this->column_indexes['operation']] == 'E') {
                            $employee = Employee::find($value[$this->column_indexes['employee_id']]);

                            if($employee == null) {
                                throw new \Exception("Funcionário {$value[1]} não encontrado");
                            }

                            $employee->processed_at = $this->convertDate($value[$this->column_indexes['processed_at']]);
                            $employee->inactivate();
                            $employee->save();
                        }
                        else {
                            throw new \Exception("Operação inválida");
                        }
                    }
                    catch(\Exception $error) {
                        $logger->warning($error->getMessage());
                        continue;
                    }
                }
            }
            Storage::move($file, str_replace('files_to_be_imported', 'imported_files', $file));
        }
    }

    private function convertDate($dateString)
    {
        $parts = explode("/", $dateString);
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
}