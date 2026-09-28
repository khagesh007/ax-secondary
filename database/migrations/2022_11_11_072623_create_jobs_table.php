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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Photos/Video Shoot','Photo Editing', 'Video Editing','Social Media Marketing','SM Paid Marketing', 'Flayer/Poster Design','Email Marketing','SMS Marketing','CRM Changes','Website Changes','Other'])->nullable();
            $table->enum('priority', ['Urgent','High','Normal','Low'])->nullable();
            $table->enum('job_status', ['Created','Waiting for Approval','Approved','Under Process','Completed','Canceled','On-Hold'])->nullable();
            $table->text('description')->nullable();
            $table->datetime('deadline_date')->nullable();
            $table->datetime('delivery_date')->nullable();

            $table->foreignId("property_id")->nullable()->constrained("users")->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId("requested_by_id")->nullable()->constrained("users")->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId("approved_by_id")->nullable()->constrained("users")->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId("assigned_to_id")->nullable()->constrained("users")->restrictOnDelete()->restrictOnUpdate();

            $table->text('note')->nullable();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->restrictOnDelete()->restrictOnUpdate();
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
        Schema::dropIfExists('jobs');
    }
};
