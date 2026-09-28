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
        Schema::create('lead_criteria_locations', function (Blueprint $table) {
            $table->id();
            $table->morphs('parentable');
            $table->foreignId('lead_criteria_id')->nullable()->constrained('lead_criterias');

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
        Schema::dropIfExists('lead_criteria_locations');
    }
};
