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
        Schema::create('blacklisted_number_histories', function (Blueprint $table) {
            $table->id();
            $table->string("model")->nullable();
            $table->foreignId("blacklisted_number_id")->nullable()->constrained("blacklisted_numbers")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("user_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->json('data')->nullable();
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
        Schema::dropIfExists('blacklisted_number_histories');
    }
};
