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
        Schema::create('temp_property_metas', function (Blueprint $table) {
            
            $table->id();
            $table->unsignedBigInteger('temp_property_id');
            $table->string('meta_key');
            $table->longText('meta_value')->nullable();
            $table->timestamps();

            $table->foreign('temp_property_id')->references('id')->on('temp_properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_property_metas');
    }
};
