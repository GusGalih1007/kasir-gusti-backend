<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'product_id' => null,
            'product_name' => 'Indomie Goreng',
            'description' => 'Indomie instant goreng',
            'slug' => 'indomie-goreng',
            'price' => 3000.00,
            'category_id' => 1,
            'brand_id' => 1,
            'supplier_id' => 1,
            'is_available' => true,
            'created_at' => null,
            'updated_at' => null,
        ]);
    }
}
