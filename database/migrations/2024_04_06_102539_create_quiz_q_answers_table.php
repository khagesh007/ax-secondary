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
        Schema::create('quiz_q_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("quiz_question_id")->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('answer',300)->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
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
        Schema::dropIfExists('quiz_q_answers');
    }
};
