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
        Schema::create('property_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId("property_id")->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string("ref_no")->nullable();
            $table->string("status")->nullable();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('property_cycles');
    }
};
