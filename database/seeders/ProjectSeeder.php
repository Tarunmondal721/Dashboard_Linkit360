<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::create(['name' => 'Project Management']);
        $owner =  Role::findByName('Owner');
        $adminRole =  Role::findByName('Admin');
        //owner
        $owner->givePermissionTo('Project Management');
        // admin role
        $adminRole->givePermissionTo('Project Management');
        //
    }
}