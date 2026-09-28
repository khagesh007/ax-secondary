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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('google_calendar_event_id')->nullable();
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->enum('priority', ['Urgent','High','Medium','Low'])->nullable();
            $table->enum('event_status', ['Scheduled','Cancelled','Successful','Unsuccessful'])->nullable();

            $table->string('title')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->text('invitation')->nullable();
            $table->boolean('reminder_status')->default(0);
            $table->boolean('email_to_attendees')->default(0);
            $table->boolean('reminder_to_attendees')->default(0);
            $table->datetime('reminder_date')->nullable();
            $table->integer('reminder')->nullable();
            $table->enum('reminder_frequency',['Minutes','Hours','Days','Weeks'])->nullable();
            $table->boolean('recurring')->default(0);
            $table->json('recurring_data')->nullable();
            $table->foreignId("agent_id")->nullable()->constrained("users")->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId("contact_id")->nullable()->constrained()->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId("lead_id")->nullable()->constrained()->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId("property_id")->nullable()->constrained()->restrictOnDelete()->restrictOnUpdate();

            $table->foreignId("created_by_id")->nullable()->constrained("users")->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->restrictOnDelete()->restrictOnUpdate();

            //$table->boolean('status')->default(1);
            $table->enum('status', ['PENDING', 'COMPLETED', 'CANCELED', 'RUNNING'])->default('PENDING');
            $table->boolean('public')->default(0);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('events');
    }
};
