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
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug");
            $table->bigInteger("folder_order")->nullable();
            $table->string("thumbnail")->nullable();
            $table->unsignedBigInteger("parent_id")->nullable();
            $table->string("model_type")->nullable();
            $table->unsignedBigInteger("model_id")->nullable();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->nullOnDelete()->cascadeOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->nullOnDelete()->cascadeOnDelete();
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
        Schema::dropIfExists('folders');
    }
};
