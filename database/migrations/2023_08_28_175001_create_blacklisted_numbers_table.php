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
        Schema::create('blacklisted_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId("blocked_by")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("unblocked_by")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('status')->nullable()->default(true);
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
        Schema::dropIfExists('blacklisted_numbers');
    }
};
