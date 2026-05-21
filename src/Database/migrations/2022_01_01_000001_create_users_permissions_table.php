<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_permissions', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('x_permissions_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('x_permissions_id')->references('id')->on('x_permissions')->onDelete('cascade');

            $table->primary(['user_id', 'x_permissions_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_permissions');
    }
};
