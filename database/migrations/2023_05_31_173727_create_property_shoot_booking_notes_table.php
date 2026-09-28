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
        Schema::create('property_shoot_booking_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_shoot_booking_id')->nullable()->constrained('property_shoot_bookings')->cascadeOnUpdate()->nullOnDelete();
            $table->text("note")->nullable();
            $table->boolean("status")->default(true);
            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('property_shoot_booking_notes');
    }
};
