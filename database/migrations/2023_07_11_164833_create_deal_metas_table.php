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
        Schema::create('deal_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id');
            $table->string('meta_key');
            $table->longText('meta_value')->nullable();
            $table->timestamps();

            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deal_meta');
    }
};
