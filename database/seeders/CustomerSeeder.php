<?php

namespace Database\Seeders;

use App\Models\Customers;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customers::create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'alamat' => 'Jl. Mangga',
            'phone' => '8888888',
            'email' => 'email@gmail.com',
            'is_member' => false
        ]);
    }
}
