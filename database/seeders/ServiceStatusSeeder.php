<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScServiceStatus;



class ServiceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data[0]=[
            'name'=>'Service Detail Created',
            'status' => 1,
        ];
        $data[1]=[
            'name'=>'Setup Server & Domain',
            'status' => 1,
        ];
        $data[2]=[
            'name'=>'Simulate Telco API [Subs, Unsubs, Renewal, MT]',
            'status' => 1,
        ];
        $data[3]=[
            'name'=>'Setup Portal & CMS',
            'status' => 1,
        ];
        $data[4]=[
            'name'=>'Integrate Telco API to Backend',
            'status' => 1,
        ];
        $data[5]=[
            'name'=>'Create Notif and Callback',
            'status' => 1,
        ];
        $data[6]=[
            'name'=>'Prepare Landing Page',
            'status' => 1,
        ];
        $data[7]=[
            'name'=>'Integrate Notif and Callback to Backend',
            'status' => 1,
        ];
        $data[8]=[
            'name'=>'Update Portal Using Business Flow',
            'status' => 1,
        ];
        $data[9]=[
            'name'=>'Test Subs And Unsubs API',
            'status' => 1,
        ];
        $data[10]=[
            'name'=>'Setup Handler for Renewal and MT',
            'status' => 1,
        ];
        $data[11]=[
            'name'=>'Test Renewal',
            'status' => 1,
        ];
        $data[12]=[
            'name'=>'Test MT',
            'status' => 1,
        ];
        $data[13]=[
            'name'=>'Integrate Subs And Unsubs API to Portal',
            'status' => 1,
        ];
        $data[14]=[
            'name'=>'Integrate Subs And Unsubs API to Landing Page',
            'status' => 1,
        ];
        $data[15]=[
            'name'=>'Test Subs And Unsubs from Portal and Landing Page',
            'status' => 1,
        ];
        $data[16]=[
            'name'=>'Check Notif and Callback on Portal',
            'status' => 1,
        ];
        $data[17]=[
            'name'=>'Setup Content & Prize for Portal',
            'status' => 1,
        ];
        $data[18]=[
            'name'=>'Prepare Report',
            'status' => 1,
        ];
        $data[19]=[
            'name'=>'Sync Report Data With Airpay Fery',
            'status' => 1,
        ];
        $data[20]=[
            'name'=>'Generate Postback URL from KB Tools',
            'status' => 1,
        ];
        $data[21]=[
            'name'=>'Create Campaign URL',
            'status' => 1,
        ];
        $data[22]=[
            'name'=>'Test Campaign URL and Postback',
            'status' => 1,
        ];
        $data[23]=[
            'name'=>'Sync Campaign Data With Report Linkit',
            'status' => 1,
        ];
        $data[24]=[
            'name'=>'Setup Cs Tools',
            'status' => 1,
        ];
        $data[25]=[
            'name'=>'Setup Monitoring',
            'status' => 1,
        ];
        $data[26]=[
            'name'=>'Setup Arpu Tools',
            'status' => 1,
        ];
        ScServiceStatus::upsert($data,['id','name'],['status']);
    }
}
