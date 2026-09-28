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
        Schema::create('listing_lead_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('status')->default(1);
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->unsigned()->default(100)->nullable();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("deleted_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();

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
        Schema::dropIfExists('listing_lead_statuses');
    }
};
