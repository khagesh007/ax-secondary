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
        Schema::create('app_logs', function (Blueprint $table) {
            $table->id();
            $table->string('title',255)->nullable();
            $table->longText('description')->nullable();
            $table->enum('type',['Bug','Info','Permission Declined','Recording Disabled','Token Expired','Other'])->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->boolean('read_status')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('app_logs');
    }
};
