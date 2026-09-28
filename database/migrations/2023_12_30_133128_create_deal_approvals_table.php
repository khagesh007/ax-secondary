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
        Schema::create('deal_approvals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('title')->nullable();
            $table->unsignedBigInteger('deal_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('action_by_id')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->string('approval_status');
            $table->text('comment')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
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
        Schema::dropIfExists('deal_approvals');
    }
};
