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
        Schema::create('kpi_other_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('agent_id');
            $table->string('model')->nullable();
            $table->string('kpi')->nullable();
            $table->string('kpi_title')->nullable();
            $table->string('total')->default(0);
            $table->string('count')->default(0);
            $table->json('data')->nullable();
            $table->json('daily_data')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('duration');
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
        Schema::dropIfExists('kpi_other_reports');
    }
};
