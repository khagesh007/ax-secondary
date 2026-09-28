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
        Schema::create('kpi_team_user_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kpi_id');
            $table->foreignId('user_id')->nullable()->constrainted('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('manager_id')->nullable()->constrainted('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrainted('groups')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('model')->nullable();
            $table->string('target')->nullable();
            $table->string('achieved')->nullable();
            $table->string('achieved_percentage')->nullable();
            $table->string('average_per_day')->nullable();
            $table->json('data')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
        Schema::dropIfExists('kpi_team_user_reports');
    }
};
