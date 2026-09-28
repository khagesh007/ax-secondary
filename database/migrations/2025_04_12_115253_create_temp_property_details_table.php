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
        Schema::create('temp_property_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temp_property_id')->constrained('temp_properties');
            $table->string('developer')->nullable();
            $table->string('charges')->nullable();
            $table->string('build_year')->nullable();
            $table->string('plot_size')->nullable();
            $table->string('plot_number')->nullable();
            $table->string('floors')->nullable();
            $table->string('occupancy')->nullable();
            $table->string('renovation')->nullable();
            $table->string('freehold')->nullable();
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
        Schema::dropIfExists('temp_property_details');
    }
};
