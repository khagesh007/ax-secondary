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
        Schema::create('call_loggables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('call_log_id');
            $table->string('call_loggable_type')->nullable();
            $table->unsignedBigInteger('call_loggable_id')->nullable();
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
        Schema::dropIfExists('call_loggables');
    }
};
