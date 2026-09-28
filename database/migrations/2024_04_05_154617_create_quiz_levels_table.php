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
        Schema::create('quiz_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('level')->default(0);
            $table->string('time')->default(0);
            $table->string('passing_grade')->nullable();
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('order')->unsigned()->default(0)->nullable();
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
        Schema::dropIfExists('quiz_levels');
    }
};
