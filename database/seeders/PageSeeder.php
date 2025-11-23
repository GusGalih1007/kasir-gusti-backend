<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $faker = Faker::create();
        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Category',
            'action'    => ['Read','Create','Update','Delete']
        ]);
        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Customer',
            'action'    => ['Read','Create','Update','Delete']
        ]);
        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Brand',
            'action'    => ['Read','Create','Update','Delete']
        ]);
        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Role',
            'action'    => ['Read','Create','Update','Delete']
        ]);
        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Users',
            'action'    => ['Read','Create','Update','Delete']
        ]);
        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Supplier',
            'action'    => ['Read','Create','Update','Delete']
        ]);
        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Product',
            'action'    => ['Read','Create','Update','Delete']
        ]);
        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Product Variant',
            'action'    => ['Read','Create','Update','Delete']
        ]);
        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Role Permission',
            'action'    => ['Read','Create','Update','Delete']
        ]);
        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Transaction',
            'action'    => ['Read','Create']
        ]);
        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Membership',
            'action'    => ['Read','Create','Update','Delete']
        ]);

        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Dashboard',
            'action'    => ['Read']
        ]);

        Page::create([
            'page_code' => $faker->regexify('[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}-[A-Za-z0-9]{10}'),
            'page_name' => 'Export',
            'action'    => ['Read', 'Create']
        ]);
    }
}
