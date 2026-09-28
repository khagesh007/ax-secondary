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
        Schema::create('property_shoot_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('preferred_time')->nullable();
            $table->text('access_information')->nullable();
            $table->boolean('dewa')->default(false);
            $table->boolean('furnished')->default(false);
            $table->boolean('cleaned')->default(false);
            $table->boolean('is_luxury')->default(false);
            $table->boolean('agent_present')->default(false);
            $table->json('requested_tags')->nullable();
            $table->enum('status', ['Waiting for Approval', 'In Progress', 'Approved', 'Rejected','Completed']);
            $table->json('pending_notification')->nullable();
            $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('cascade');
            $table->foreignId('requested_by_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by_id')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('property_shoot_bookings');
    }
};
