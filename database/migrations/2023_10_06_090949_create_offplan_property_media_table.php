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
        Schema::create('offplan_property_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId("offplan_property_id")->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string("type")->nullable();
            $table->string("slug_to_map")->nullable();
            $table->string("is_vr_enabled")->nullable();
            $table->string("path")->nullable();
            $table->boolean('will_delete')->nullable()->default(true);
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
        Schema::dropIfExists('offplan_property_media');
    }
};
