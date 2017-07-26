<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Employee;
use App\Company;
use Illuminate\Support\Facades\Storage;
use Excel;

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
        
        if(count($files) > 0) {
            foreach($files as $file) {
                $data = Excel::load(Storage::url('app/'.$file), function($reader) {
			    })->get()->toArray();

                if(!empty($data) && count($data) > 0) {
                    foreach ($data as $key => $value) {
                        try {
                            if($value[4] == 'I') {
                                $this->info("iniciando importação");
                                if(Company::find($value[0]) == null) {
                                    throw new \Exception("Empresa {$value[0]} não encontrada");
                                }
                                $this->info("empresa encontrada");

                                $employee = new Employee();
                                $employee->id = $value[1];
                                $employee->company_id = $value[0];
                                $employee->name = $value[2];
                                $employee->processed_at = $this->convertDate($value[3]);
                                $employee->status = 1;
                                $employee->save();
                            }
                            else if($value[4] == 'E') {
                                $employee = Employee::find($value[1]);

                                if($employee == null) {
                                    throw new \Exception("Funcionário {$value[1]} não encontrado");
                                }

                                $employee->status = 0;
                                $employee->save();
                            }
                        }
                        catch(\Exception $error) {
                            $this->info($error->getMessage());
                            continue;
                        }
                    }
                }
            }
        }
    }

    private function convertDate($dateString)
    {
        $parts = explode("/", $dateString);
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
}