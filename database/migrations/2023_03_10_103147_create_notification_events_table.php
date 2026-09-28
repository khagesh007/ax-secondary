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
        Schema::create('notification_events', function (Blueprint $table) {
            $table->id();
            $table->string('title',200)->nullable();
            $table->json('medium')->nullable(); // ['WEB','TELEGRAM','EMAIL'] etc
            $table->json('roles')->nullable();
            $table->string('class_name')->nullable();
            $table->json('excluded')->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('notification_events');
    }
};
