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
        Schema::create('telegram_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('model')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('user_status')->nullable();
            $table->string('chat_id')->nullable();
            $table->text('message')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->default('sent')->nullable();
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
        Schema::dropIfExists('telegram_notifications');
    }
};
