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
        Schema::connection('log_db')->create('portal_listing_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->unsignedBigInteger('portal_id')->nullable()->index();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 100)->nullable();
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
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
        Schema::dropIfExists('portal_listing_timelines');
    }
};
