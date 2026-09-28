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
        Schema::create('kpi_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('agent_id');
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
        Schema::dropIfExists('kpi_targets');
    }
};
