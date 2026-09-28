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
        Schema::create('portals', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("short_name")->nullable();
            $table->string("slug")->nullable();
            $table->unsignedMediumInteger("quota")->nullable();
            $table->string("thumbnail")->nullable();
            $table->string("image")->nullable();
            $table->string("xml_url")->nullable();
            $table->text("token")->nullable();
            $table->text("token_abu_dhabi")->nullable();
            $table->text("token_commercial")->nullable();
            $table->boolean("status")->default(true);
            $table->foreignId("created_by_id")->nullable(true)->constrained("users");
            $table->foreignId("updated_by_id")->nullable(true)->constrained("users");
            $table->softDeletes();
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
        Schema::dropIfExists('portals');
    }
};
