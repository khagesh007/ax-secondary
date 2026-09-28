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
        Schema::create('property_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId("property_id")->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string("title")->nullable();
            $table->string("description")->nullable();
            $table->string("path")->nullable();
            $table->string("original")->nullable();
            $table->string("thumbnail")->nullable();
            $table->string("password")->nullable();
            $table->string("note")->nullable();
            $table->unsignedBigInteger("order")->nullable();
            $table->enum("type", ["Photos", "Floor Plan", "Other"])->default("Photos");
            $table->boolean("temp_image")->default(0);
            $table->boolean("not_uploaded_on_s3")->default(0);
            $table->boolean("status")->default(true);
            $table->string("temp_id")->nullable();
            $table->string("media_updated")->nullable();
            $table->foreignId('deleted_by_id')->nullable()->constrained('users')->after('updated_by_id');
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
        Schema::dropIfExists('property_media');
    }
};
