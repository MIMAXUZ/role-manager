<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('x_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('x_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('roles_permissions', function (Blueprint $table) {
            $table->bigInteger('x_roles_id')->unsigned();
            $table->bigInteger('x_permissions_id')->unsigned();

            $table->foreign('x_roles_id')->references('id')->on('x_roles')->onDelete('cascade');
            $table->foreign('x_permissions_id')->references('id')->on('x_permissions')->onDelete('cascade');

            $table->primary(['x_roles_id', 'x_permissions_id']);
        });

        Schema::create('users_roles', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('x_roles_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('x_roles_id')->references('id')->on('x_roles')->onDelete('cascade');

            $table->primary(['user_id', 'x_roles_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles_permissions');
        Schema::dropIfExists('users_roles');
        Schema::dropIfExists('x_permissions');
        Schema::dropIfExists('x_roles');
    }
};
