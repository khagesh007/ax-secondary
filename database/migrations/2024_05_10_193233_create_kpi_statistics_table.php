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
        Schema::create('kpi_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kpi_id');
            $table->unsignedBigInteger('user_id');
            $table->string('target');
            $table->string('achieved');
            $table->string('achieved_percentage');
            $table->string('average_per_day');
            $table->json('data')->nullable();
            $table->date('start_date');
            $table->date('end_date');
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
        Schema::dropIfExists('kpi_statistics');
    }
};
