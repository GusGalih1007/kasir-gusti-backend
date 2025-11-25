<?php

namespace Database\Seeders;

use App\Models\Membership;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Membership::create([
            'membership' => 'Regular',
            'Benefit' => 'None',
            'discount' => 0,
            'expiration_period' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => null,
            'updated_by' => null,
        ]);
    }
}
