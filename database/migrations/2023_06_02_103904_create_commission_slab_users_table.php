<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_slab_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('commission_slab_id')->unsigned()->nullable()->index();
            $table->bigInteger('role_id')->unsigned()->nullable()->index();
            $table->foreign('role_id')
                    ->references('id')->on('roles')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->bigInteger('created_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('updated_by_id')->unsigned()->nullable()->index();
            $table->foreign('commission_slab_id')
                    ->references('id')->on('commission_slabs')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('created_by_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('updated_by_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commission_slab_users');
    }
};
