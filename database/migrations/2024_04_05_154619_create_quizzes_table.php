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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trial_quiz_id')->nullable();
            $table->string('name',300)->nullable();
            $table->text('description')->nullable();
            $table->foreignId("quiz_level_id")->nullable()->constrained("quiz_levels")->cascadeOnUpdate()->nullOnDelete();
            $table->longText('rules')->nullable();
            $table->string('thumbnail',300)->nullable();
            $table->json('meta')->nullable();
            $table->string('theme',50)->nullable();
            $table->string('rating',30)->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('quizzes');
    }
};
