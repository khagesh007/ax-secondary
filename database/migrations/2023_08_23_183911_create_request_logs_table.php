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
        Schema::connection('log_db')->create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('slug',255)->nullable();
            $table->string('ip_address')->nullable();
            $table->text('url')->nullable();
            $table->string('method')->nullable();
            $table->json('content')->nullable();
            $table->integer('response_code')->nullable();
            $table->bigInteger('request_count')->default(0);
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
        Schema::dropIfExists('request_logs');
    }
};
