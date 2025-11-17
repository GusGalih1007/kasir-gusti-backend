<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('payment_method');
            $table->timestamp('token_expires_at')->nullable()->after('snap_token');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'token_expires_at']);
        });
    }
};