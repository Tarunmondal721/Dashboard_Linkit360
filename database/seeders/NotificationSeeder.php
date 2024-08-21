<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::create(['name' => 'Notification Management']);
        Permission::create(['name' => 'Create Notification']);
        Permission::create(['name' => 'Show Notification']);
        $owner =  Role::findByName('Owner');
        $adminRole =  Role::findByName('Admin');
        //owner
        $owner->givePermissionTo('Notification Management');
        $owner->givePermissionTo('Create Notification');
        $owner->givePermissionTo('Show Notification');
        // admin role
        $adminRole->givePermissionTo('Notification Management');
        $adminRole->givePermissionTo('Create Notification');
        $adminRole->givePermissionTo('Show Notification');
    }
}
