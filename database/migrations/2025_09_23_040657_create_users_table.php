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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username', 50);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone', 20);
            $table->unsignedBigInteger('role_id');
            $table->string('status', 30);
            $table->timestamp('last_login')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('updated_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
