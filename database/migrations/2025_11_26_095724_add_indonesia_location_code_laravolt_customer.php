<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->char('province_code', 2)->after('last_name')->nullable();
            $table->char('city_code', 4)->after('province_code')->nullable();
            $table->char('district_code', 7)->after('city_code')->nullable();
            $table->char('village_code', 10)->after('district_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['province_code', 'city_code', 'district_code', 'village_code']);
        });
    }
};
