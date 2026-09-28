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
        Schema::create('drive_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->string('image_id');
            $table->string('folder_id');
            $table->string('name')->nullable();
            $table->string("path")->nullable();
            $table->boolean("status")->default(true);
            $table->string("type")->nullable();
            $table->boolean("temp_image")->default(1);
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
        Schema::dropIfExists('drive_photos');
    }
};
