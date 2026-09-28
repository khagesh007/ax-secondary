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
        Schema::create('developers', function (Blueprint $table) {
            $table->id();
            $table->string('short_name')->nullable();
            $table->string('full_name')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email',255)->nullable();
            $table->string('website',255)->nullable();
            $table->string('logo_path',255)->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('developers');
    }
};
