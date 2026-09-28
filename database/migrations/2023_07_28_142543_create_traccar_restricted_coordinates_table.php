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
        Schema::create('traccar_restricted_coordinates', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('radius')->nullable();
            $table->string('zoom')->nullable();
            $table->string('stay_time')->nullable(); // in mins
            $table->boolean('status')->default(1);
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->bigInteger('created_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('updated_by_id')->unsigned()->nullable()->index();
            $table->foreign('user_id')
                ->references('id')->on('users');
            $table->foreign('created_by_id')
                ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('updated_by_id')
                ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traccar_restricted_coordinates');
    }
};
