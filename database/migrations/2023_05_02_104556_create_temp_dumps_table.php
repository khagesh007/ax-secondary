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
        Schema::create('temp_dumps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); //api name ex. BITRIX, BAYUT ETC
            $table->string('unique_id')->nullable(); //unique id related to api res id or page something
            $table->string('start_from')->nullable();
            $table->string('end_at')->nullable();
            $table->string('status')->nullable();
            $table->json('data')->nullable();
            $table->json('flags')->nullable();
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
        Schema::dropIfExists('temp_dumps');
    }
};
