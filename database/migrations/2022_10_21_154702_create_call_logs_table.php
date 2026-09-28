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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('lead_follow_up_id')->nullable()->constrained('lead_follow_ups')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('contact_follow_up_id')->nullable()->constrained('contact_follow_ups')->onDelete('cascade')->onUpdate('cascade');
            $table->string('client_number')->nullable(); //numbers
            $table->string('call_type')->nullable(); //incoming outgoing
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('internal_user_id')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('call_started_at')->nullable();
            $table->timestamp('call_answered_at')->nullable();
            $table->timestamp('call_ended_at')->nullable();
            $table->string('call_duration')->nullable();
            $table->string('call_status')->nullable();
            $table->string('call_sid')->nullable();
            $table->text('call_recording_url')->nullable();
            $table->string('call_recording_sid')->nullable();
            $table->json('json_dump')->nullable();
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
        Schema::dropIfExists('call_logs');
    }
};
