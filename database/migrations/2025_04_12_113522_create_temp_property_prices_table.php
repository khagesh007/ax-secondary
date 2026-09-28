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
        Schema::create('temp_property_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId("temp_property_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal("price", 10, 0)->nullable();
            $table->string('prices')->nullable();
            $table->boolean('price_on_application')->default(false);
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
        Schema::dropIfExists('temp_property_prices');
    }
};
