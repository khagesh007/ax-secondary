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
        Schema::create('property_amenity_portals', function (Blueprint $table) {
            $table->id();
            $table->foreignId("property_amenity_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("portal_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("title")->nullable();
            $table->string("type")->nullable();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('property_amenity_portals');
    }
};
