<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = [
            'Kreative Multimedia',
            'Kreative Bersama',
            'Yatta',
            'Waki',
            'PASS',
            'Linkit360',
            'Click Multimedia',
            'Linkit MENA',
            'Linkit EU',
            'Linkit Global',
            'Linkit Africa',
        ];

        foreach($companies as $company){
            Company::create([
                'name'=>$company,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ]);
        }
    }

}
