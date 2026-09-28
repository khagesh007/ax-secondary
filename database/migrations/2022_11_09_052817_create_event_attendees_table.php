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
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId("event_id")->constrained()->restrictOnUpdate()->restrictOnDelete();
            $table->foreignId("user_id")->constrained("users")->restrictOnUpdate()->restrictOnDelete();
            $table->boolean('confirmed')->default(0);
            $table->text('note')->nullable();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->restrictOnUpdate()->restrictOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->restrictOnUpdate()->restrictOnDelete();
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
        Schema::dropIfExists('event_attendees');
    }
};
