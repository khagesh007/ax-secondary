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
        Schema::create('kpi_team_agent_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->nullable()->constrainted('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrainted('groups')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrainted('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('model')->nullable();
            $table->string('kpi')->nullable();
            $table->string('kpi_title')->nullable();
            $table->string('total')->default(0);
            $table->string('count')->default(0);
            $table->json('data')->nullable();
            $table->json('daily_data')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('duration')->nullable();
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
        Schema::dropIfExists('kpi_team_agent_reports');
    }
};
