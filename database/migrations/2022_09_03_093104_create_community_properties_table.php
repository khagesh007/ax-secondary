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
        Schema::create('community_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId("region_id")->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("community_id")->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("sub_community_id")->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string("name");
            $table->string("alternate_name")->nullable();
            //$table->string("parent_name")->nullable();
            $table->string("slug")->nullable();
            $table->string("description")->nullable();
            $table->string("path_name")->nullable();
            $table->string("alternate_path_name")->nullable();
            $table->boolean("default_use")->default(true);
            $table->boolean("alternate_use")->default(true);
            $table->string("latitude")->nullable();
            $table->string("longitude")->nullable();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->boolean("status")->default(true);
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
        Schema::dropIfExists('community_properties');
    }
};
