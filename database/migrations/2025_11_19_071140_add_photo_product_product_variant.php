<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('photo')->after('is_available')->nullable();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->text('photo')->after('stock_qty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('photo');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};
