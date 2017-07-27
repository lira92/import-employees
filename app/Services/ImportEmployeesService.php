<?php

namespace App\Services;

use App\Employee;
use App\Company;
use Illuminate\Support\Facades\Storage;
use Excel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ImportEmployeesService
{
    public function __construct(Logger $logger)
    {
        $this->importEmployeesLogger = $logger;
    }

    private $column_indexes = [
        'employee_id' => 1,
        'company_id' => 0,
        'name' => 2,
        'processed_at' => 3,
        'operation' => 4
    ];

    public function import()
    {
        $files = Storage::files("files_to_be_imported");
        
        if(count($files) == 0) {
            return "Nenhum arquivo para ser importado";
        }

        foreach($files as $file) {
            $data = Excel::load(Storage::url('app/'.$file), function($reader) {
            }, 'ISO 8859-1')->get()->toArray();

            if(!empty($data) && count($data) > 0) {
                foreach ($data as $key => $value) {
                    try {
                        $rowIndex = $key + 1;
                        if(Company::find($value[$this->column_indexes['company_id']]) == null) {
                            throw new \Exception("Empresa {$value[$this->column_indexes['company_id']]} não encontrada");
                        }
                        if($value[$this->column_indexes['name']] == null) {
                            throw new \Exception("Nome do Funcionário não definido na linha {$rowIndex}");
                        }
                        if($value[$this->column_indexes['processed_at']] == null) {
                            throw new \Exception("Data de processamento não definida na linha {$rowIndex}");
                        }

                        if($value[$this->column_indexes['operation']] == 'I') {
                            $this->fillEmployee($value)->save();
                        }
                        else if($value[$this->column_indexes['operation']] == 'E') {
                            $employee = Employee::find($value[$this->column_indexes['employee_id']]);

                            if($employee == null) {
                                throw new \Exception("Funcionário {$value[$this->column_indexes['employee_id']]} não encontrado");
                            }

                            $employee->processed_at = $this->convertDate($value[$this->column_indexes['processed_at']]);
                            $employee->inactivate();
                            $employee->save();
                        }
                        else {
                            throw new \Exception("Operação inválida na linha {$rowIndex}");
                        }
                    }
                    catch(\Exception $error) {
                        $this->importEmployeesLogger->warning($error->getMessage());
                        continue;
                    }
                }
            }
            Storage::move($file, str_replace('files_to_be_imported', 'imported_files', $file));
        }
        return "Importação executada com sucesso";
    }

    private function convertDate($dateString)
    {
        $parts = explode("/", $dateString);
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }

    private function fillEmployee($row)
    {
        $employee = new Employee();
        $employee->id = $row[$this->column_indexes['employee_id']];
        $employee->company_id = $row[$this->column_indexes['company_id']];
        $employee->name = $row[$this->column_indexes['name']];
        $employee->processed_at = $this->convertDate($row[$this->column_indexes['processed_at']]);
        return $employee;
    }
}