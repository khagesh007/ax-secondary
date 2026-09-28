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
        Schema::create('quiz_media', function (Blueprint $table) {
            $table->id();
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string("title")->nullable();
            $table->string("description")->nullable();
            $table->string("path")->nullable();
            $table->string("original")->nullable();
            $table->string("thumbnail")->nullable();
            $table->string("extension",40)->nullable();
            $table->string("size",30)->nullable();
            $table->unsignedBigInteger("order")->nullable();
            $table->enum("type", ["Photos", "Document", "Other"])->default("Document");
            $table->boolean("status")->default(true);
            $table->string("temp_id")->nullable();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('quiz_media');
    }
};
