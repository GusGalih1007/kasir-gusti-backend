<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('is_used')->default(false)->after('token_expires_at');
            $table->string('previous_tokens')->nullable()->after('is_used'); // Store previous tokens for audit
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['is_used', 'previous_tokens']);
        });
    }
};