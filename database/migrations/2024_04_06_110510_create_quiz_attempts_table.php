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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("quiz_id")->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->integer('score')->default(0);
            $table->integer('attempted_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('skipped_answers')->default(0);
            $table->string('time_taken')->nullable();
            $table->string('total_marks',30)->default(0);
            $table->string('result')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('rating',30)->nullable();
            $table->string('feedback',300)->nullable();
            $table->enum('status', ['completed', 'pending','in_progress'])->default('in_progress');
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
        Schema::dropIfExists('quiz_attempts');
    }
};
