<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         //admin
        $admin = User::create([
            'name' => 'System',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
            'user_type' => 'admin',
        ]);
        $admin->assignRole('admin');
         $user = User::create([
            'name' => 'sub system',
            'email' => 'user@uer.com',
            'password' => Hash::make('12345678'),
            'user_type' => 'user',
        ]);
        $user->assignRole('admin');

    }
}
