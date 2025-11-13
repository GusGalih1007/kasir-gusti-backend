<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductVariant::create([
            'variant_id' => null,
            'product_id' => 1,
            'variant_name' => 'Goreng Original',
            'price' => 3000.00,
            'sku' => 'SKU-NUMB-123',
            'stock_qty' => 100,
            'created_at' => null,
            'updated_at' => null,
        ]);
        ProductVariant::create([
            'variant_id' => null,
            'product_id' => 1,
            'variant_name' => 'Goreng Rendang',
            'price' => 3000.00,
            'sku' => 'SKU-NUMB-456',
            'stock_qty' => 100,
            'created_at' => null,
            'updated_at' => null,
        ]);
    }
}
