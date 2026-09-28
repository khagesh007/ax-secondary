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
        Schema::create('kpi_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->nullable()->constrainted('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrainted('groups')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('model');
            $table->string('kpi');
            $table->string('target');
            $table->string('duration');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_processed')->nullable()->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('kpi_teams');
    }
};
