<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('permissions')->truncate();
        Schema::enableForeignKeyConstraints();

        $permissions = [

            // Roles
            ['group' => 'roles', 'name' => 'view_roles', 'title' => 'View Roles', 'guard_name' => 'web'],
            ['group' => 'roles', 'name' => 'add_role', 'title' => 'Add Role', 'guard_name' => 'web'],
            ['group' => 'roles', 'name' => 'edit_role', 'title' => 'Edit Role', 'guard_name' => 'web'],
            ['group' => 'roles', 'name' => 'delete_role', 'title' => 'Delete Role', 'guard_name' => 'web'],

            // Users
            ['group' => 'users', 'name' => 'view_users', 'title' => 'View Users', 'guard_name' => 'web'],
            ['group' => 'users', 'name' => 'add_user', 'title' => 'Add User', 'guard_name' => 'web'],
            ['group' => 'users', 'name' => 'edit_user', 'title' => 'Edit User', 'guard_name' => 'web'],
            ['group' => 'users', 'name' => 'delete_user', 'title' => 'Delete User', 'guard_name' => 'web'],
            // menus
            ['group' => 'menus', 'name' => 'view_menus', 'title' => 'View Menus', 'guard_name' => 'web'],
            ['group' => 'menus', 'name' => 'add_menu', 'title' => 'Add Menu', 'guard_name' => 'web'],
            ['group' => 'menus', 'name' => 'edit_menu', 'title' => 'Edit Menu', 'guard_name' => 'web'],
            ['group' => 'menus', 'name' => 'delete_menu', 'title' => 'Delete Menu', 'guard_name' => 'web'],
            // Category
            ['group' => 'categories', 'name' => 'view_categories', 'title' => 'View Categories', 'guard_name' => 'web'],
            ['group' => 'categories', 'name' => 'add_category', 'title' => 'Add Category', 'guard_name' => 'web'],
            ['group' => 'categories', 'name' => 'edit_category', 'title' => 'Edit Category', 'guard_name' => 'web'],
            ['group' => 'categories', 'name' => 'delete_category', 'title' => 'Delete Category', 'guard_name' => 'web'],
             // table
            ['group' => 'tables', 'name' => 'view_tables', 'title' => 'View tables', 'guard_name' => 'web'],
            ['group' => 'tables', 'name' => 'add_table', 'title' => 'Add Table', 'guard_name' => 'web'],
            ['group' => 'tables', 'name' => 'edit_table', 'title' => 'Edit Table', 'guard_name' => 'web'],
            ['group' => 'tables', 'name' => 'delete_table', 'title' => 'Delete Table', 'guard_name' => 'web'],


        ];
        Permission::insert($permissions);

    }
}
