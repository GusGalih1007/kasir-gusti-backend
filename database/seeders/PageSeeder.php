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
        $randomRegexString = $faker->regexify('[A-Za-z0-9]{10}'); // Generates a random string matching the regex pattern
        Page::create([
            'page_code' => $randomRegexString,
            'page_name' => 'Category',
            'action'    => 'Create,Read,Update,Delete'
        ]);
        Page::create([
            'page_code' => $randomRegexString,
            'page_name' => 'Customer',
            'action'    => 'Create,Read,Update,Delete'
        ]);
        Page::create([
            'page_code' => $randomRegexString,
            'page_name' => 'Brand',
            'action'    => 'Create,Read,Update,Delete'
        ]);
        Page::create([
            'page_code' => $randomRegexString,
            'page_name' => 'Role',
            'action'    => 'Create,Read,Update,Delete'
        ]);
        Page::create([
            'page_code' => $randomRegexString,
            'page_name' => 'Users',
            'action'    => 'Create,Read,Update,Delete'
        ]);
        Page::create([
            'page_code' => $randomRegexString,
            'page_name' => 'Supplier',
            'action'    => 'Create,Read,Update,Delete'
        ]);
        Page::create([
            'page_code' => $randomRegexString,
            'page_name' => 'Product',
            'action'    => 'Create,Read,Update,Delete'
        ]);
        Page::create([
            'page_code' => $randomRegexString,
            'page_name' => 'Product Variant',
            'action'    => 'Create,Read,Update,Delete'
        ]);
        Page::create([
            'page_code' => $randomRegexString,
            'page_name' => 'Page',
            'action'    => 'Read'
        ]);
        Page::create([
            'page_code' => $randomRegexString,
            'page_name' => 'Page ROle Action',
            'action'    => 'Read'
        ]);
    }
}
