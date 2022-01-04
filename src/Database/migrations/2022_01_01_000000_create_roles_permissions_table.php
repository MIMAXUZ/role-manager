<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of permission 
            $table->string('slug')->unique(); //slug of permission, permission is unique, ex: create-post
            $table->timestamps();
           // $table->timestamps(); if you want timestamps uncomment it
        });
        Schema::create('x_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of role
            $table->string('slug')->unique(); //slug of role, permission is unique, ex: admin
            $table->timestamps();
            // $table->timestamps(); if you want timestamps uncomment it
        });
        
        Schema::create('roles_permissions', function (Blueprint $table) {
            $table->bigInteger('x_roles_id')->unsigned();
            $table->bigInteger('x_permissions_id')->unsigned();

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('x_roles_id')->references('id')->on('x_roles')->onDelete('cascade');
            $table->foreign('x_permissions_id')->references('id')->on('x_permissions')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['x_roles_id','x_permissions_id']);
        });

        Schema::create('users_roles', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('x_roles_id')->unsigned();

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('x_roles_id')->references('id')->on('x_roles')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['user_id','x_roles_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('x_permissions');
        Schema::dropIfExists('x_roles');
        Schema::dropIfExists('roles_permissions');
        Schema::dropIfExists('users_roles');
    }
}
