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
        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('quiz_question_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('quiz_q_answer_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->boolean('is_skipped')->default(false);
            $table->integer('marks')->default(0);
            $table->integer('negative_marks')->default(0);
            $table->integer('time')->default(0);
            $table->foreignId('created_by_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('cascade');
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
        Schema::dropIfExists('quiz_attempt_answers');
    }
};
