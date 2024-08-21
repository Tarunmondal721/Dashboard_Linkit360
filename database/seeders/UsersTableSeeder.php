<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Utility;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    // php artisan permission:create-permission "Pivot Management" web
    // php artisan permission:create-permission "Tools Management" web
    // php artisan permission:create-permission "Tools Show" web

   public function run()
    {

        $arrPermissions = [



            'Dashboard',

            /** Reports Permissions define  */
                'Reports Management',
                'Report Summary',
                'Reporting Details',
                'PNL Summary',
                'PNL Detail',
                'Monitor Operational',
                'Monitor ROI',
                'Reconcialiation Media',
                'Adnet Report',
            /** Reports Permissions end */

            /** analytic Permissions define  */
                'Analytic Management',
                'Ads Monitoring',
                'Revenue Monitoring',
                'Revenue Alert',
                'ROI Report',
                'Log Performance',
            /** analytic Permissions end */

            /** Management Permissions define  */
                'Management',
                'Company Management',
                'Currency Management',
                'Operator Management',
            /** Management Permissions end */


            /** Finance Permissions define  */
                'Finance Management',
                'Revenue Reconcile',
                'Target Revenue',
            /** Finance Permissions end */

            /** Activity Log Permissions define  */
                'Activity Log Management',
                'User Activity',
                'System Activity',
            /** Activity Log Permissions end */

            /** Service Catalogue Permissions define  */
                'Service Catalogue',
                'Add New Service',
                'Service List',
            /** Service Catalogue Permissions end  */

            /** Product  Permissions define  */
                'Product Management',
                'Add New Product',
                'Product List',
            /** Product Permissions end */

            /** Log File Permissions define  */
                'Log File Management',
                'Cron Log',
            /** Log File Permissions end */

            /** Users Permissions define  */
                'Manage Users',
                'Create User',
                'Edit User',
                'Delete User',
            /** Users Permissions end  */

                /** Roles Permissions define  */
                'Manage Roles',
                'Create Role',
                'Edit Role',
                'Delete Role',
            /** Roles Permissions end  */
            'System Settings',

        ];

        foreach($arrPermissions as $ap)
        {
            Permission::create(['name' => $ap]);
        }

        $adminRole = Role::create(
            [
                'name' => 'Owner',
                'created_by' => 0,
            ]
        );

        $adminPermissions = [


            'Dashboard',

            /** Reports Permissions define  */
            'Reports Management',
            'Report Summary',
            'Reporting Details',
            'PNL Summary',
            'PNL Detail',
            'Monitor Operational',
            'Monitor ROI',
            'Reconcialiation Media',
            'Adnet Report',
            /** Reports Permissions end */

            /** analytic Permissions define  */
                'Analytic Management',
                'Ads Monitoring',
                'Revenue Monitoring',
                'Revenue Alert',
                'ROI Report',
                'Log Performance',
            /** analytic Permissions end */

            /** Management Permissions define  */
                'Management',
                'Company Management',
                'Currency Management',
                'Operator Management',
            /** Management Permissions end */


            /** Finance Permissions define  */
                'Finance Management',
                'Revenue Reconcile',
                'Target Revenue',
            /** Finance Permissions end */

            /** Activity Log Permissions define  */
                'Activity Log Management',
                'User Activity',
                'System Activity',
            /** Activity Log Permissions end */

              /** Service Catalogue Permissions define  */
              'Service Catalogue',
              'Add New Service',
              'Service List',
            /** Service Catalogue Permissions end  */
            /** Product  Permissions define  */
                'Product Management',
                'Add New Product',
                'Product List',
            /** Product Permissions end */
            /** Log File Permissions define  */
                'Log File Management',
                'Cron Log',
            /** Log File Permissions end */

            /** Users Permissions define  */
                'Manage Users',
                'Create User',
                'Edit User',
                'Delete User',
            /** Users Permissions end  */

                /** Roles Permissions define  */
                'Manage Roles',
                'Create Role',
                'Edit Role',
                'Delete Role',
            /** Roles Permissions end  */
            'System Settings',


        ];

        foreach($adminPermissions as $ap)
        {
            $permission = Permission::findByName($ap);
            $adminRole->givePermissionTo($permission);
        }
        $admin = User::create(
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'user_name' => 'USER1234',
                'password' => Hash::make('1234'),
                'type' => 'Owner',
                'lang' => 'en',
                'created_by' => 0,
            ]
        );
        $admin->assignRole($adminRole);
        $admin->defaultEmail();
       // $admin->userDefaultData();

        // print_r($admin);die('+++++');

        $clientRole        = Role::create(
            [
                'name' => 'Client',
                'created_by' => 0,
            ]
        );
        $clientPermissions = [
            "Dashboard",

        ];
        foreach($clientPermissions as $ap)
        {
            $permission = Permission::findByName($ap);
            $clientRole->givePermissionTo($permission);
        }

        $userRole        = Role::create(
            [
                'name' => 'linkITStaff',
                'created_by' => $admin->id,
            ]
        );
        $userPermissions = [
            'Report Summary',

        ];

        foreach($userPermissions as $ap)
        {
            $permission = Permission::findByName($ap);
            $userRole->givePermissionTo($permission);
        }
        $user = User::create(
            [
                'name' => 'User',
                'email' => 'user@example.com',
                'user_name' => 'USER1234',
                'password' => Hash::make('1234'),
                'type' => 'linkITStaff',
                'lang' => 'en',
                'created_by' => $admin->id,
            ]
        );
        $user->assignRole($userRole);

    }
}
