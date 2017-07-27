<?php

use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 6854,
                'name' => 'Company #1',
                'created_at' => date('Y-m-d H:s')
            ],
            [
                'id' => 7199,
                'name' => 'Company #2',
                'created_at' => date('Y-m-d H:s')
            ]
        ];

        DB::table('companies')->insert($data);
    }
}
