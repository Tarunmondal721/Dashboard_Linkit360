<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class SummarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'Summary Management']);
        Permission::create(['name' => 'Summary Arpu']);
        $owner =  Role::findByName('Owner');
        $adminRole =  Role::findByName('Admin');
        //owner
        $owner->givePermissionTo('Summary Management');
        $owner->givePermissionTo('Summary Arpu');
        // admin role
        $adminRole->givePermissionTo('Summary Management');
        $adminRole->givePermissionTo('Summary Arpu');
    }
}
