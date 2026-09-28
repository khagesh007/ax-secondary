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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("quiz_id")->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('question',300)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_multiple')->default(false);
            $table->boolean('negative_marking')->default(false);
            $table->boolean('negative_marks')->default(false);
            $table->integer('marks')->default(0);
            $table->integer('skipped_marks')->default(0);
            $table->integer('time')->default(0);
            $table->string('correct_answer')->nullable();
            $table->string('image_url',300)->nullable();
            $table->string('video_url',300)->nullable();
            $table->string('youtube_url',300)->nullable();
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
        Schema::dropIfExists('quiz_questions');
    }
};
