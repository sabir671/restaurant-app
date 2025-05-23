<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Schema::disableForeignKeyConstraints();
        DB::table('role_has_permissions')->truncate();
        Schema::enableForeignKeyConstraints();

        // Permissions
        $permissions = Permission::get()->pluck('id');

        // Assign permissions to roles
        $admin = Role::updateOrCreate(['name' => 'admin', 'title' => 'Admin', 'is_deleteable' => 0]);
        $user = Role::updateOrCreate(['name' => 'user', 'title' => 'User', 'is_deleteable' => 0]);

        $admin->permissions()->sync($permissions);
        $user->permissions()->sync($permissions);
    }
}
