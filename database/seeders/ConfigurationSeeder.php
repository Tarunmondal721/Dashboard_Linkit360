<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $configurations = [
            [
                'key' => 'middleware_url_api',
                'value' => '18.142.8.108:9999/api/v1/'
            ],
            [
                'key' => 'timeout_settings',
                'value' => "30"
            ]
        ];
        foreach ($configurations as $config) {
            Configuration::create($config);
        }
    }
}
