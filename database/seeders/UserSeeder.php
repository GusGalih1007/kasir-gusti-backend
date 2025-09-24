<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Users;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Users::create([
            'username' => 'Admin Gusti',
            'email' => 'admingusti@gmail.com',
            'password' => bcrypt('123456789'),
            'first_name' => 'Admin',
            'last_name' => 'Gusti',
            'phone' => '088822221111',
            'role_id' => 1,
            'status' => 'Active',
            'last_login' => null,
            'created_by' => null,
            'updated_by' => null
        ]);
    }
}
