<?php

use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $langauge_arr = [
            [
                'name' => 'Limited',
                'display_name' => 'Limited',
                'description' => 'Access to all screens except billing',
            ],
            [
                'name' => 'Full Access',
                'display_name' => 'Full Access',
                'description' => 'Access to main menu pages.',
            ],

        ];
        \DB::table('permissions')->delete();
        \DB::table('permissions')->insert($langauge_arr);

        $langauge_arr = [
            [
                'name' => 'owner',
                'display_name' => 'Owner',
                'description' => 'Store Owner.',
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Widget Co. Employee.',
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'User Administrator',
            ],

        ];
        \DB::table('roles')->delete();

        \DB::table('roles')->insert($langauge_arr);

    }
}
