<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\AdminUser::create([
            'name' => 'Admin',
            'email' => 'admin@skicapital.com',
            'password' => bcrypt('admin123'),
            'role' => 'super_admin',
            'status' => 1,
        ]);
    }
}
