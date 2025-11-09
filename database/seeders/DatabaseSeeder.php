<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Supplier;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(RoleSeeder::class);
        // $this->call(UserSeeder::class);
        // $this->call(CategorySeeder::class);
        // $this->call(CustomerSeeder::class);
        // $this->call(BrandSeeder::class);
        // $this->call(SupplierSeeder::class);
        $this->call(PageSeeder::class);
    }
}
