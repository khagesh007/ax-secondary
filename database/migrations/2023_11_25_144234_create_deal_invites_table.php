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
        Schema::create('deal_invites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->string('invite_code')->unique();
            $table->boolean('form_submitted')->default(false);
            $table->unsignedBigInteger('invited_to_id')->nullable();
            $table->unsignedBigInteger('invited_by_id')->nullable();
            $table->boolean('is_signed')->default(false);
            $table->timestamp('form_submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deal_invites');
    }
};
