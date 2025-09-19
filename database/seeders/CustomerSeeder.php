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
            'name' => 'Customer 1',
            'alamat' => 'Jl. Mangga',
            'phone' => '8888888',
            'email' => 'email@gmail.com',
            'is_member' => false
        ]);
    }
}
