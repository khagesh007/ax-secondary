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
        Schema::create('p_f_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('webhook_id')->nullable();
            $table->string('type')->nullable();
            $table->json('payload')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->string('status', 50)->default('pending'); // pending, success, failed
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
        Schema::dropIfExists('p_f_webhook_logs');
    }
};
